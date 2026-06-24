<?php
// src/Control/CCambioPassword.php
//
// CONTROLLORE — cambio password obbligatorio al primo accesso.
//   mostraForm() -> ?action=formCambioPassword  (GET)
//   esegui()     -> ?action=cambioPassword        (POST)
//
// Accessibile solo a un utente LOGGATO che ha il flag "deve cambiare
// password" attivo. Dopo il cambio, il flag viene azzerato e l'utente
// prosegue verso la sua dashboard.

class CCambioPassword
{
    // -------------------------------------------------------
    // mostraForm()
    // -------------------------------------------------------
    public function mostraForm(): void
    {
        // Dev'esserci un utente loggato in fase di "primo accesso".
        if (Session::getUserId() === null) {
            header('Location: index.php?action=login');
            exit;
        }
        (new ViewCambioPassword())->mostraForm();
    }

    // -------------------------------------------------------
    // esegui()
    // -------------------------------------------------------
    public function esegui(): void
    {
        if (Session::getUserId() === null) {
            header('Location: index.php?action=login');
            exit;
        }

        $view    = new ViewCambioPassword();
        $attuale = $view->getAttuale();
        $nuova   = $view->getNuova();
        $nuova2  = $view->getNuova2();

        // 1. VALIDAZIONE
        if ($attuale === '' || $nuova === '' || $nuova2 === '') {
            Session::setFlash('errore', 'Compila tutti i campi.');
            header('Location: index.php?action=formCambioPassword');
            exit;
        }
        if (strlen($nuova) < 8) {
            Session::setFlash('errore', 'La nuova password deve avere almeno 8 caratteri.');
            header('Location: index.php?action=formCambioPassword');
            exit;
        }
        if ($nuova !== $nuova2) {
            Session::setFlash('errore', 'Le due nuove password non coincidono.');
            header('Location: index.php?action=formCambioPassword');
            exit;
        }

        $pm     = PersistentManager::getInstance();
        $utente = $pm->load(Utente::class, Session::getUserId());
        if ($utente === null) {
            Session::logout();
            return;
        }

        // 2. La password temporanea inserita deve essere quella corrente.
        if (!$utente->verificaPassword($attuale)) {
            Session::setFlash('errore', 'La password attuale non è corretta.');
            header('Location: index.php?action=formCambioPassword');
            exit;
        }

        // 3. La nuova non può essere uguale alla temporanea.
        if ($utente->verificaPassword($nuova)) {
            Session::setFlash('errore', 'La nuova password deve essere diversa da quella temporanea.');
            header('Location: index.php?action=formCambioPassword');
            exit;
        }

        // 4. AGGIORNO password e azzero il flag. setPassword fa l'hash.
        $utente->setPassword($nuova);
        $utente->setDeveCambiarePassword(false);
        $pm->store($utente);

        // 5. Tolgo il flag dalla sessione e mando alla dashboard del ruolo.
        Session::remove('deveCambiarePassword');
        Session::setFlash('successo', 'Password aggiornata. Benvenuto in CondoFix!');

        $action = match (Session::getRuolo()) {
            'amministratore' => 'dashboardAdmin',
            'fornitore'      => 'dashboardFornitore',
            'condomino'      => 'dashboardCondomino',
            default          => 'login',
        };
        header('Location: index.php?action=' . $action);
        exit;
    }
}
