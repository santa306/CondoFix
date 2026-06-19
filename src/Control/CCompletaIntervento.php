<?php
// src/Control/CCompletaIntervento.php
//
// CONTROLLORE — operazione di sistema "Completa lavoro" (SSD 5, fornitore).
//
// Transizione di stato del workflow:  IN CORSO -> COMPLETATO.
//
// Gemella di CAvviaIntervento: stesso schema (carica, verifica proprieta',
// verifica stato di partenza con instanceof, crea nuovo stato trasferendo
// i dati comuni, salva, redirect con flash). Cambia solo:
//   - stato di partenza richiesto: InCorso
//   - stato di arrivo: Completato
//   - il timestamp impostato: dataCompletamento

class CCompletaIntervento
{
    public function esegui(): void
    {
        // 1. PERMESSI
        Session::requireRole('fornitore');

        // 2. INPUT (dalla View)
        $view = new ViewCompletaIntervento();
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

        // 4. CONTROLLO DI PROPRIETA'
        $fornitoreAssegnato = $vecchio?->getFornitore();
        if ($fornitoreAssegnato === null
            || $fornitoreAssegnato->getId() !== Session::getUserId()) {
            Session::setFlash('errore', 'Questo lavoro non e\' assegnato a te.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        // 5. CONTROLLO DI STATO: posso completare solo un lavoro IN CORSO
        if (!($vecchio instanceof InCorso)) {
            Session::setFlash('errore',
                'Il lavoro non e\' "In corso": impossibile completarlo.');
            header('Location: index.php?action=dettaglioInterventoFornitore&id=' . $id);
            exit;
        }

        // 6. TRANSIZIONE: creo il nuovo stato e trasferisco i dati comuni
        $nuovo = new Completato();
        $nuovo->setPriorita($vecchio->getPriorita());
        $nuovo->setFornitore($vecchio->getFornitore());
        $nuovo->setDataCompletamento(new DateTime());   // timestamp di chiusura lavori

        $intervento->setStato($nuovo);
        $pm->update();

        // 7. ESITO
        Session::setFlash('successo', 'Lavoro completato!');
        header('Location: index.php?action=dettaglioInterventoFornitore&id=' . $id);
        exit;
    }
}

