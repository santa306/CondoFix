<?php
// src/Control/CAccettaIntervento.php
//
// OPERAZIONE DI SISTEMA: accetta(idIntervento, priorita, idFornitore)
// ATTORE: Amministratore.
// TRANSIZIONE: Presentato -> Accettato.
//
// È l'azione dietro il pulsante APPROVA della pagina di dettaglio
// (sketch pag. 3): l'admin sceglie priorità e fornitore, e la segnalazione
// diventa un lavoro assegnato. Stesso schema di CNegaIntervento, con due
// dati in più (priorità e fornitore) letti dal form.

class CAccettaIntervento
{
    // Valori di priorità ammessi (coerenti con la tendina del template).
    private const PRIORITA_VALIDE = ['alta', 'media', 'bassa'];

    public function esegui(): void
    {
        // 1. INPUT (sempre tramite la View, mai $_POST diretto)
        $view         = new ViewGestioneIntervento();
        $idIntervento = $view->getIdIntervento();
        $priorita     = $view->getPriorita();
        $idFornitore  = $view->getIdFornitore();

        // 2. PERMESSI
        Session::requireRole('amministratore');

        // 3. VALIDAZIONE degli input
        if ($idIntervento <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: index.php?action=dashboardAdmin');
            exit;
        }
        if (!in_array($priorita, self::PRIORITA_VALIDE, true)) {
            Session::setFlash('errore', 'Seleziona una priorità valida.');
            header('Location: index.php?action=dettaglioIntervento&id=' . $idIntervento);
            exit;
        }
        if ($idFornitore <= 0) {
            Session::setFlash('errore', 'Seleziona un fornitore.');
            header('Location: index.php?action=dettaglioIntervento&id=' . $idIntervento);
            exit;
        }

        $pm = PersistentManager::getInstance();

        // 4. FOUNDATION — carico l'intervento e verifico lo stato.
        $intervento = $pm->load(Intervento::class, $idIntervento);
        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento inesistente.');
            header('Location: index.php?action=dashboardAdmin');
            exit;
        }
        // Si accetta solo un intervento ancora "Presentato".
        if (!($intervento->getStato() instanceof Presentato)) {
            Session::setFlash('errore', 'Questo intervento non è più in stato "Presentato".');
            header('Location: index.php?action=dettaglioIntervento&id=' . $idIntervento);
            exit;
        }

        // Carico il fornitore scelto. Con SINGLE_TABLE sugli utenti, verifico
        // che l'id corrisponda davvero a un Fornitore (e non a un altro ruolo).
        $fornitore = $pm->load(Fornitore::class, $idFornitore);
        if (!($fornitore instanceof Fornitore)) {
            Session::setFlash('errore', 'Fornitore non valido.');
            header('Location: index.php?action=dettaglioIntervento&id=' . $idIntervento);
            exit;
        }

        // 5. TRANSIZIONE DI STATO: creo il nuovo stato Accettato, lo popolo
        //    e lo aggancio all'intervento. Il vecchio stato Presentato viene
        //    sostituito; grazie al cascade su Intervento->stato, l'update salva.
        $accettato = new Accettato();
        $accettato->setPriorita($priorita);
        $accettato->setFornitore($fornitore);
        $intervento->setStato($accettato);
        $pm->update();

        // 6. ESITO
        Session::setFlash('successo', 'Intervento accettato e assegnato al fornitore.');
        header('Location: index.php?action=dashboardAdmin');
        exit;
    }
}
