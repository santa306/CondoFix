<?php
// index.php
//
// FRONT CONTROLLER / ROUTER di CondoFix.
// Unico punto di ingresso: ogni richiesta passa da qui con ?action=nomeAzione.

// --- 1. AMBIENTE -----------------------------------------------------
require_once __DIR__ . '/bootstrap.php';

// Le classi Control e View non hanno namespace: le includo a mano.
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

        // ---- Dashboard ----
        case 'dashboardAdmin':
            (new CDashboard())->admin();
            break;
        case 'dashboardFornitore':
            (new CDashboard())->fornitore();
            break;
        case 'dashboardCondomino':
            (new CDashboardCondomino())->mostra();
            break;

        // ---- Amministratore: nega intervento (Control pronto, manca la View) ----
        case 'negaIntervento':
            (new CNegaIntervento())->esegui();
            break;

        // ---- Condomino: nuova segnalazione (Blocco A) ----
        case 'formPresentaIntervento':
            (new CPresentaIntervento())->mostraForm();
            break;
        case 'presentaIntervento':
            (new CPresentaIntervento())->esegui();
            break;

        // ---- Condomino: dettaglio intervento (Blocco A) ----
        case 'dettaglioIntervento':
            (new CDettaglioIntervento())->mostra();
            break;

        // ---- Condomino: modifica segnalazione presentata (Blocco A) ----
        case 'formModificaIntervento':
            (new CModificaIntervento())->mostraForm();
            break;
        case 'modificaIntervento':
            (new CModificaIntervento())->esegui();
            break;

        // ---- Azioni ancora DA IMPLEMENTARE ----
        case 'accettaIntervento':
        case 'allegaFattura':
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
    http_response_code(500);
    echo 'Si e\' verificato un errore: ' . htmlspecialchars($e->getMessage());
}
