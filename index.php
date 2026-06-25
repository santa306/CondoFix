<?php
// index.php
//
// FRONT CONTROLLER / ROUTER di CondoFix.
// Unico punto di ingresso: ogni richiesta passa da qui con ?action=nomeAzione.
// --- 1. AMBIENTE -----------------------------------------------------
require_once __DIR__ . '/bootstrap.php';
// Le classi Control e View non hanno namespace: le includo a mano.
foreach (glob(__DIR__ . '/src/Control/*.php') as $file) { require_once $file; }
require_once __DIR__ . '/src/View/ViewBase.php';
foreach (glob(__DIR__ . '/src/View/*.php')    as $file) { require_once $file; }
// --- 2. AZIONE -------------------------------------------------------
$action = $_GET['action'] ?? 'landing';
// --- 3. SMISTAMENTO --------------------------------------------------
try {
    switch ($action) {
        // ---- Autenticazione (PRONTO) ----
        case 'landing':
            (new CLanding())->mostra();
            break;
        case 'login':
            (new CLogin())->mostraForm();
            break;
        case 'formRegistrazione':
            (new CRegistrazione())->mostraForm();
            break;
        case 'registraAdmin':
            (new CRegistrazione())->esegui();
            break;
        case 'formCambioPassword':
            (new CCambioPassword())->mostraForm();
            break;
        case 'cambioPassword':
            (new CCambioPassword())->esegui();
            break;
        // ---- Profilo personale (tutti i ruoli) ----
        case 'profilo':
            (new CProfilo())->mostra();
            break;
        case 'profiloPassword':
            (new CProfilo())->cambiaPassword();
            break;
        case 'profiloFoto':
            (new CProfilo())->cambiaFoto();
            break;
        case 'vetrina':
            (new CVetrinaPubblica())->mostra();
            break;
        case 'vetrinaDettaglio':
            (new CVetrinaPubblica())->mostraDettaglio();
            break;
        case 'doLogin':                    // POST del form di login
            (new CLogin())->esegui();
            break;
        case 'logout':
            (new CLogin())->logout();
            break;
        // ---- Dashboard ----
        // Blocco B: dashboard amministratore (ora collegata al Control vero)
        case 'dashboardAdmin':
            (new CDashboardAdmin())->mostra();
            break;
        case 'formCreaIntervento':
            (new CCreaIntervento())->mostraForm();
            break;
        case 'creaIntervento':
            (new CCreaIntervento())->esegui();
            break;
        // ---- Amministratore: lista lavoratori (fornitori) ----
        case 'listaLavoratori':
            (new CListaLavoratori())->mostra();
            break;
        case 'formCreaLavoratore':
            (new CCreaLavoratore())->mostraForm();
            break;
        case 'creaLavoratore':
            (new CCreaLavoratore())->esegui();
            break;
        case 'eliminaLavoratore':
            (new CEliminaLavoratore())->esegui();
            break;
        // ---- Amministratore: lista condomini + nuovo condominio ----
        case 'listaCondomini':
            (new CListaCondomini())->mostra();
            break;
        case 'formCreaCondominio':
            (new CListaCondomini())->mostraForm();
            break;
        case 'creaCondominio':
            (new CListaCondomini())->esegui();
            break;
        case 'eliminaCondominio':
            (new CEliminaCondominio())->esegui();
            break;
        // ---- Amministratore: dettaglio condominio + gestione condÃ²mini ----
        case 'dettaglioCondominio':
            (new CDettaglioCondominio())->mostra();
            break;
        case 'formCreaCondomino':
            (new CDettaglioCondominio())->mostraFormCondomino();
            break;
        case 'creaCondomino':
            (new CDettaglioCondominio())->creaCondomino();
            break;
        case 'eliminaCondomino':
            (new CDettaglioCondominio())->eliminaCondomino();
            break;
        case 'dashboardFornitore':
            (new CDashboardFornitore())->mostra();
            break;
        case 'dettaglioInterventoFornitore':
            (new CDettaglioInterventoFornitore())->mostra();
            break;
        case 'avviaIntervento':
            (new CAvviaIntervento())->esegui();
            break;
        case 'completaIntervento':
            (new CCompletaIntervento())->esegui();
            break;
        case 'aggiungiNota':
            (new CAggiungiNota())->esegui();
            break;
        case 'caricaFoto':
            (new CCaricaFoto())->esegui();
            break;
        case 'dashboardCondomino':
            (new CDashboardCondomino())->mostra();
            break;
        // ---- Amministratore: nega intervento (Blocco B) ----
        case 'negaIntervento':
            (new CNegaIntervento())->esegui();
            break;
        // ---- Amministratore: dettaglio intervento (Blocco B) ----
        case 'dettaglioInterventoAdmin':
            (new CDettaglioInterventoAdmin())->mostra();
            break;
        // ---- Amministratore: accetta intervento (Blocco B) ----
        case 'accettaIntervento':
            (new CAccettaIntervento())->esegui();
            break;
        // ---- Amministratore: allega fattura (Blocco B) ----
        case 'allegaFattura':
            (new CAllegaFattura())->esegui();
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











