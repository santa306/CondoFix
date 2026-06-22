<?php
// src/Control/CNegaIntervento.php
//
// OPERAZIONE DI SISTEMA: nega(idIntervento, motivazione)
// ATTORE: Amministratore.
// TRANSIZIONE: Presentato -> Negato (stato terminale).

class CNegaIntervento
{
    public function esegui(): void
    {
        // 1. INPUT
        $view         = new ViewGestioneIntervento();
        $idIntervento = (int) $view->getIdIntervento();
        $motivazione  = $view->getMotivazione();

        // 3. PERMESSI
        Session::requireRole('amministratore');

        // 2. VALIDAZIONE
        if ($idIntervento <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: index.php?action=dashboardAdmin');
            exit;
        }

        // 4. FOUNDATION
        $pm         = PersistentManager::getInstance();
        $intervento = $pm->load(Intervento::class, $idIntervento);
        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento inesistente.');
            header('Location: index.php?action=dashboardAdmin');
            exit;
        }

        // Si nega solo un intervento ancora "Presentato"
        if (!($intervento->getStato() instanceof Presentato)) {
            Session::setFlash('errore', 'Questo intervento non è in stato "Presentato".');
            header('Location: index.php?action=dashboardAdmin');
            exit;
        }

        $negato = new Negato();
        if (!empty(trim((string) $motivazione))) {
            $negato->setMotivazione(trim($motivazione));
        }
        $intervento->setStato($negato);

        // Nota automatica di avanzamento (con timestamp automatico).
        $nota = new Nota();
        $nota->setTesto('Segnalazione rifiutata.');
        $intervento->addNota($nota);

        $pm->update();

        // 5. ESITO
        Session::setFlash('successo', 'Intervento rifiutato.');
        header('Location: index.php?action=dashboardAdmin');
        exit;
    }
}
