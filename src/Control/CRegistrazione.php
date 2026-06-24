<?php
// src/Control/CRegistrazione.php
//
// CONTROLLORE — registrazione di un nuovo Amministratore.
//   mostraForm() -> ?action=formRegistrazione   (GET: mostra il form)
//   esegui()     -> ?action=registraAdmin        (POST: crea l'account)
//
// Pagina pubblica: nessun requireRole, è accessibile a chi non è loggato.
// Schema standard: input dalla View -> validazione -> Foundation -> esito.

class CRegistrazione
{
    // -------------------------------------------------------
    // mostraForm() — mostra il form vuoto
    // -------------------------------------------------------
    public function mostraForm(): void
    {
        (new ViewRegistrazione())->mostraForm();
    }

    // -------------------------------------------------------
    // esegui() — crea il nuovo amministratore
    // -------------------------------------------------------
    public function esegui(): void
    {
        $view = new ViewRegistrazione();

        // 1. INPUT
        $nome      = $view->getNome();
        $cognome   = $view->getCognome();
        $email     = $view->getEmail();
        $telefono  = $view->getTelefono();
        $password  = $view->getPassword();
        $password2 = $view->getPassword2();

        // I valori da ripopolare in caso di errore (mai le password).
        $vecchi = [
            'nome'     => $nome,
            'cognome'  => $cognome,
            'email'    => $email,
            'telefono' => $telefono,
        ];

        // 2. VALIDAZIONE
        if ($nome === '' || $cognome === '' || $email === '' || $password === '') {
            Session::setFlash('errore', 'Compila tutti i campi obbligatori.');
            $view->mostraForm($vecchi);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('errore', 'Inserisci un indirizzo email valido.');
            $view->mostraForm($vecchi);
            return;
        }
        if (strlen($password) < 8) {
            Session::setFlash('errore', 'La password deve avere almeno 8 caratteri.');
            $view->mostraForm($vecchi);
            return;
        }
        if ($password !== $password2) {
            Session::setFlash('errore', 'Le due password non coincidono.');
            $view->mostraForm($vecchi);
            return;
        }

        $pm = PersistentManager::getInstance();

        // 3. EMAIL GIA' USATA? (l'email è lo username, deve essere unica)
        if ($pm->utente()->findByEmail($email) !== null) {
            Session::setFlash('errore', 'Esiste già un account con questa email.');
            $view->mostraForm($vecchi);
            return;
        }

        // 4. CREO l'amministratore. setPassword fa l'hash bcrypt da solo.
        $admin = new Amministratore();
        $admin->setNome($nome);
        $admin->setCognome($cognome);
        $admin->setEmail($email);
        $admin->setTelefono($telefono !== '' ? $telefono : null);
        $admin->setPassword($password);

        // 5. SALVO
        $pm->store($admin);

        // 6. ESITO: porto l'utente al login con un messaggio di conferma.
        Session::setFlash('successo', 'Account creato! Ora puoi accedere.');
        header('Location: index.php?action=login');
        exit;
    }
}
