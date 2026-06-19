<?php
// src/Control/CAvviaIntervento.php
//
// CONTROLLORE — operazione di sistema "Avvia lavoro" (SSD 5, fornitore).
//
// Transizione di stato del workflow:  ACCETTATO -> IN CORSO.
//
// Questa verticale non ha una pagina propria: riceve l'id via POST dai
// pulsanti "Inizia lavoro" (dashboard e dettaglio), esegue la transizione
// e fa redirect con un messaggio flash. Pattern POST-redirect-GET.
//
// Schema della transizione (dalla guida del corso):
//   1. carica l'intervento
//   2. verifica con instanceof che lo stato di partenza sia quello giusto
//   3. crea il NUOVO stato e trasferisci i dati comuni (priorita', fornitore)
//   4. setStato() + update()
// Il vecchio stato non va modificato: gli stati sono oggetti distinti,
// la storia del workflow e' rappresentata dal cambio di oggetto.

class CAvviaIntervento
{
    public function esegui(): void
    {
        // 1. PERMESSI
        Session::requireRole('fornitore');

        // 2. INPUT (dalla View, mai $_POST diretto)
        $view = new ViewAvviaIntervento();
        $id   = $view->getIdIntervento();

        if ($id <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        // 3. FOUNDATION: carico l'intervento
        $pm = PersistentManager::getInstance();
        $intervento = $pm->load(Intervento::class, $id);

        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento non trovato.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        $vecchio = $intervento->getStato();

        // 4. CONTROLLO DI PROPRIETA': il lavoro deve essere assegnato a me
        $fornitoreAssegnato = $vecchio?->getFornitore();
        if ($fornitoreAssegnato === null
            || $fornitoreAssegnato->getId() !== Session::getUserId()) {
            Session::setFlash('errore', 'Questo lavoro non e\' assegnato a te.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        // 5. CONTROLLO DI STATO: posso avviare solo un lavoro ACCETTATO
        if (!($vecchio instanceof Accettato)) {
            Session::setFlash('errore',
                'Il lavoro non e\' in stato "Da fare": impossibile avviarlo.');
            header('Location: index.php?action=dettaglioInterventoFornitore&id=' . $id);
            exit;
        }

        // 6. TRANSIZIONE: creo il nuovo stato e trasferisco i dati comuni
        $nuovo = new InCorso();
        $nuovo->setPriorita($vecchio->getPriorita());
        $nuovo->setFornitore($vecchio->getFornitore());
        $nuovo->setDataAvvio(new DateTime());   // timestamp di avvio lavori

        $intervento->setStato($nuovo);
        $pm->update();   // flush: il cascade persist salva anche il nuovo stato

        // 7. ESITO
        Session::setFlash('successo', 'Lavoro avviato!');
        header('Location: index.php?action=dettaglioInterventoFornitore&id=' . $id);
        exit;
    }
}

