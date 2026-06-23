<?php
// src/Control/CDettaglioInterventoAdmin.php
//
// CONTROLLORE — operazione di sistema "visualizza dettaglio di un intervento".
// ATTORE: Amministratore.
//
// Caso d'uso (sketch pag. 3): l'admin clicca un intervento dalla dashboard
// (o dalla lista lavori) e vede la scheda completa: titolo, stato, condominio,
// data, descrizione e foto. Se l'intervento è ancora "Presentato", la pagina
// mostra i pulsanti NEGA e APPROVA che avviano la prima biforcazione del
// workflow (Presentato -> Negato | Accettato).
//
// Questa pagina è di sola lettura: le azioni (accetta/nega) sono operazioni
// separate, gestite dai rispettivi Control (CAccettaIntervento / CNegaIntervento).
// Qui ci limitiamo a caricare l'intervento e a passarlo alla View.

class CDettaglioInterventoAdmin
{
    public function mostra(): void
    {
        // 1. INPUT — l'id arriva dalla URL (?id=...), letto SOLO tramite la View.
        $view         = new ViewGestioneIntervento();
        $idIntervento = $view->getIdIntervento();

        // 2. PERMESSI
        Session::requireRole('amministratore');

        // 3. VALIDAZIONE
        if ($idIntervento <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: index.php?action=dashboardAdmin');
            exit;
        }

        // 4. FOUNDATION — carico l'intervento per id.
        $pm         = PersistentManager::getInstance();
        $intervento = $pm->load(Intervento::class, $idIntervento);
        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento inesistente.');
            header('Location: index.php?action=dashboardAdmin');
            exit;
        }

        // Per il form di APPROVA servono i fornitori selezionabili.
        // Li passo alla View così, se lo stato è "Presentato", il template
        // può mostrare la tendina di scelta del fornitore.
        $fornitori = $pm->utente()->findAllFornitori();

        // 5. OUTPUT
        (new ViewGestioneIntervento())->mostra($intervento, $fornitori);
    }
}
