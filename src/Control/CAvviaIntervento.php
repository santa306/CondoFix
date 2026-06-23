<?php
// src/Control/CAvviaIntervento.php
//
// CONTROLLORE — operazione di sistema "Avvia lavoro".
//
// Transizione di stato del workflow:  ACCETTATO -> IN CORSO.
//
// Possono avviare un lavoro DUE ruoli:
//   - il Fornitore assegnato (esegue materialmente il lavoro)
//   - l'Amministratore (supervisione: puo' intervenire sull'avanzamento)
// Il comportamento si adatta al ruolo: il fornitore puo' agire solo sui
// lavori a lui assegnati (controllo di proprieta'); l'amministratore puo'
// agire su qualsiasi lavoro. Anche le pagine di ritorno cambiano per ruolo.
//
// Pattern POST-redirect-GET: id via POST, transizione, redirect con flash.

class CAvviaIntervento
{
    public function esegui(): void
    {
        // 1. PERMESSI: fornitore OPPURE amministratore
        Session::requireAnyRole(['fornitore', 'amministratore']);
        $ruolo = Session::getRuolo();
        $isAdmin = ($ruolo === 'amministratore');

        // Pagine di ritorno in base al ruolo
        $tornaDashboard = $isAdmin
            ? 'index.php?action=dashboardAdmin'
            : 'index.php?action=dashboardFornitore';
        $tornaDettaglio = function (int $id) use ($isAdmin) {
            return $isAdmin
                ? 'index.php?action=dettaglioInterventoAdmin&id=' . $id
                : 'index.php?action=dettaglioInterventoFornitore&id=' . $id;
        };

        // 2. INPUT (dalla View)
        $view = new ViewAvviaIntervento();
        $id   = $view->getIdIntervento();

        if ($id <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: ' . $tornaDashboard);
            exit;
        }

        // 3. FOUNDATION: carico l'intervento
        $pm = PersistentManager::getInstance();
        $intervento = $pm->load(Intervento::class, $id);

        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento non trovato.');
            header('Location: ' . $tornaDashboard);
            exit;
        }

        $vecchio = $intervento->getStato();

        // 4. CONTROLLO DI PROPRIETA' — solo per il fornitore.
        //    L'amministratore puo' agire su qualsiasi lavoro, quindi salta
        //    questo controllo.
        if (!$isAdmin) {
            $fornitoreAssegnato = $vecchio?->getFornitore();
            if ($fornitoreAssegnato === null
                || $fornitoreAssegnato->getId() !== Session::getUserId()) {
                Session::setFlash('errore', 'Questo lavoro non e\' assegnato a te.');
                header('Location: ' . $tornaDashboard);
                exit;
            }
        }

        // 5. CONTROLLO DI STATO: posso avviare solo un lavoro ACCETTATO
        if (!($vecchio instanceof Accettato)) {
            Session::setFlash('errore',
                'Il lavoro non e\' in stato "Da fare": impossibile avviarlo.');
            header('Location: ' . $tornaDettaglio($id));
            exit;
        }

        // 6. TRANSIZIONE: nuovo stato + dati comuni
        $nuovo = new InCorso();
        $nuovo->setPriorita($vecchio->getPriorita());
        $nuovo->setFornitore($vecchio->getFornitore());
        $nuovo->setDataAvvio(new DateTime());

        $intervento->setStato($nuovo);

        // Nota automatica di avanzamento (con timestamp automatico).
        $nota = new Nota();
        $nota->setTesto('Lavoro avviato.');
        $intervento->addNota($nota);

        $pm->update();

        // 7. ESITO
        Session::setFlash('successo', 'Lavoro avviato!');
        header('Location: ' . $tornaDettaglio($id));
        exit;
    }
}
