<?php
// index.php
//
// FRONT CONTROLLER / ROUTER di CondoFix.
// Unico punto di ingresso: ogni richiesta passa da qui con ?action=nomeAzione.
//
// Questa versione e' allineata ai Control che ESISTONO davvero ora:
//   - CLogin       (login/logout)            -> pronto
//   - CDashboard   (3 dashboard placeholder) -> pronto
//   - CNegaIntervento                        -> pronto (ma serve la sua View)
// Le altre action restano elencate ma, finche' la classe Control non esiste,
// rispondono con un messaggio chiaro invece di un fatal error.

// --- 1. AMBIENTE -----------------------------------------------------
// bootstrap.php include vendor/autoload.php (Doctrine + Smarty) e crea $entityManager.
require_once __DIR__ . '/bootstrap.php';

// Le classi Control e View non hanno namespace: le includo a mano.
// (Se preferisci, puoi aggiungere src/Control e src/View al classmap di
//  composer.json e fare "composer dump-autoload": allora questi due foreach
//  diventano superflui. Per ora il require esplicito e' il modo piu' robusto.)
foreach (glob(__DIR__ . '/src/Control/*.php') as $file) { require_once $file; }
foreach (glob(__DIR__ . '/src/View/*.php')    as $file) { require_once $file; }

// --- 2. AZIONE -------------------------------------------------------
$action = $_GET['action'] ?? 'login';

// --- 3. SMISTAMENTO --------------------------------------------------
try {
    switch ($action) {

        // ---- Autenticazione (PRONTO) ----
        case 'login':
            (new CLogin())->mostraForm();
            break;

        case 'doLogin':                    // POST del form di login
            (new CLogin())->esegui();
            break;

        case 'logout':
            (new CLogin())->logout();
            break;

        // ---- Dashboard Amministratore (Blocco B — verticale 1) ----
        case 'dashboardAdmin':
            (new CDashboardAdmin())->mostra();
            break;

        // ---- Dashboard fornitore/condomino (PRONTO, placeholder) ----

        case 'dashboardFornitore':
            (new CDashboard())->fornitore();
            break;

        case 'dashboardCondomino':
            (new CDashboard())->condomino();
            break;

        // ---- Amministratore: dettaglio intervento (Blocco B — verticale 2) ----
        case 'dettaglioIntervento':
            (new CDettaglioIntervento())->mostra();
            break;

        // ---- Amministratore: accetta intervento (Blocco B — verticale 3) ----
        case 'accettaIntervento':
            (new CAccettaIntervento())->esegui();
            break;

        // ---- Amministratore: allega fattura (Blocco B — verticale 4) ----
        case 'allegaFattura':
            (new CAllegaFattura())->esegui();
            break;

        // ---- Amministratore: nega intervento (Blocco B) ----
        case 'negaIntervento':
            (new CNegaIntervento())->esegui();
            break;

        // ---- Azioni ancora DA IMPLEMENTARE ----
        // Elencate per memoria: appena crei la classe Control corrispondente,
        // sostituisci la riga con la chiamata vera (come per il login).
        case 'formPresentaIntervento':
        case 'presentaIntervento':
        case 'avviaIntervento':
        case 'completaIntervento':
        case 'aggiungiNota':
        case 'caricaFoto':
            http_response_code(501);
            echo 'Funzione non ancora implementata: ' . htmlspecialchars($action);
            break;

        // ---- Azione sconosciuta ----
        default:
            http_response_code(404);
            echo 'Pagina non trovata (action: ' . htmlspecialchars($action) . ').';
            break;
    }

} catch (Throwable $e) {
    // In sviluppo mostriamo il messaggio per fare debug.
    // In produzione: logga $e e mostra una pagina generica.
    http_response_code(500);
    echo 'Si e\' verificato un errore: ' . htmlspecialchars($e->getMessage());
}
