<?php
// src/Control/CCompletaIntervento.php
//
// CONTROLLORE — operazione di sistema "Completa lavoro".
//
// Transizione di stato del workflow:  IN CORSO -> COMPLETATO.
//
// Gemella di CAvviaIntervento. Possono completare un lavoro DUE ruoli:
//   - il Fornitore assegnato (solo sui propri lavori)
//   - l'Amministratore (su qualsiasi lavoro, per supervisione)
// Comportamento e pagine di ritorno si adattano al ruolo.

class CCompletaIntervento
{
    public function esegui(): void
    {
        // 1. PERMESSI: fornitore OPPURE amministratore
        Session::requireAnyRole(['fornitore', 'amministratore']);
        $ruolo = Session::getRuolo();
        $isAdmin = ($ruolo === 'amministratore');

        $tornaDashboard = $isAdmin
            ? 'index.php?action=dashboardAdmin'
            : 'index.php?action=dashboardFornitore';
        $tornaDettaglio = function (int $id) use ($isAdmin) {
            return $isAdmin
                ? 'index.php?action=dettaglioInterventoAdmin&id=' . $id
                : 'index.php?action=dettaglioInterventoFornitore&id=' . $id;
        };

        // 2. INPUT (dalla View)
        $view = new ViewCompletaIntervento();
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

        // 4. CONTROLLO DI PROPRIETA' — solo per il fornitore
        if (!$isAdmin) {
            $fornitoreAssegnato = $vecchio?->getFornitore();
            if ($fornitoreAssegnato === null
                || $fornitoreAssegnato->getId() !== Session::getUserId()) {
                Session::setFlash('errore', 'Questo lavoro non e\' assegnato a te.');
                header('Location: ' . $tornaDashboard);
                exit;
            }
        }

        // 5. CONTROLLO DI STATO: posso completare solo un lavoro IN CORSO
        if (!($vecchio instanceof InCorso)) {
            Session::setFlash('errore',
                'Il lavoro non e\' "In corso": impossibile completarlo.');
            header('Location: ' . $tornaDettaglio($id));
            exit;
        }

        // 6. TRANSIZIONE: nuovo stato + dati comuni
        $nuovo = new Completato();
        $nuovo->setPriorita($vecchio->getPriorita());
        $nuovo->setFornitore($vecchio->getFornitore());
        $nuovo->setDataCompletamento(new DateTime());

        $intervento->setStato($nuovo);

        // Nota automatica di avanzamento (con timestamp automatico).
        $nota = new Nota();
        $nota->setTesto('Lavoro completato.');
        // Autore della nota automatica: il fornitore che compie l'azione.
        $nota->setAutore($pm->load(Utente::class, Session::getUserId()));
        $intervento->addNota($nota);

        $pm->update();

        // 7. ESITO
        Session::setFlash('successo', 'Lavoro completato!');
        header('Location: ' . $tornaDettaglio($id));
        exit;
    }
}
