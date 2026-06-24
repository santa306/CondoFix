<?php
// src/Control/CProfilo.php
//
// CONTROLLORE — profilo personale dell'utente loggato (qualsiasi ruolo).
//   mostra()          -> ?action=profilo            (dati + form)
//   cambiaPassword()  -> ?action=profiloPassword     (POST)
//   cambiaFoto()      -> ?action=profiloFoto         (POST upload immagine)
//
// L'utente può SOLO cambiare la propria password e la propria foto profilo.
// Nient'altro è modificabile.

class CProfilo
{
    private const MAX_BYTES = 5 * 1024 * 1024;   // 5 MB
    private const TIPI_AMMESSI = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    // -------------------------------------------------------
    // mostra() — pagina profilo
    // -------------------------------------------------------
    public function mostra(): void
    {
        Session::requireAuth();

        $pm     = PersistentManager::getInstance();
        $utente = $pm->load(Utente::class, Session::getUserId());
        if ($utente === null) { Session::logout(); return; }

        (new ViewProfilo())->mostra($utente);
    }

    // -------------------------------------------------------
    // cambiaPassword()
    // -------------------------------------------------------
    public function cambiaPassword(): void
    {
        Session::requireAuth();

        $view    = new ViewProfilo();
        $attuale = $view->getAttuale();
        $nuova   = $view->getNuova();
        $nuova2  = $view->getNuova2();

        if ($attuale === '' || $nuova === '' || $nuova2 === '') {
            Session::setFlash('errore', 'Compila tutti i campi della password.');
            header('Location: index.php?action=profilo');
            exit;
        }
        if (strlen($nuova) < 8) {
            Session::setFlash('errore', 'La nuova password deve avere almeno 8 caratteri.');
            header('Location: index.php?action=profilo');
            exit;
        }
        if ($nuova !== $nuova2) {
            Session::setFlash('errore', 'Le due nuove password non coincidono.');
            header('Location: index.php?action=profilo');
            exit;
        }

        $pm     = PersistentManager::getInstance();
        $utente = $pm->load(Utente::class, Session::getUserId());
        if ($utente === null) { Session::logout(); return; }

        if (!$utente->verificaPassword($attuale)) {
            Session::setFlash('errore', 'La password attuale non è corretta.');
            header('Location: index.php?action=profilo');
            exit;
        }

        $utente->setPassword($nuova);
        $pm->store($utente);

        Session::setFlash('successo', 'Password aggiornata con successo.');
        header('Location: index.php?action=profilo');
        exit;
    }

    // -------------------------------------------------------
    // cambiaFoto() — upload immagine profilo
    // -------------------------------------------------------
    public function cambiaFoto(): void
    {
        Session::requireAuth();

        $view = new ViewProfilo();
        $file = $view->getFotoCaricata();   // array di $_FILES['foto'] o null

        // Validazione file (stesso schema delle foto intervento).
        if ($file === null || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('errore', 'Caricamento immagine fallito. Riprova.');
            header('Location: index.php?action=profilo');
            exit;
        }
        if ($file['size'] <= 0 || $file['size'] > self::MAX_BYTES) {
            Session::setFlash('errore', 'L\'immagine supera la dimensione massima (5 MB).');
            header('Location: index.php?action=profilo');
            exit;
        }
        $mime = mime_content_type($file['tmp_name']);
        if (!isset(self::TIPI_AMMESSI[$mime])) {
            Session::setFlash('errore', 'Formato non valido: carica un\'immagine (jpg, png, webp, gif).');
            header('Location: index.php?action=profilo');
            exit;
        }
        $estensione = self::TIPI_AMMESSI[$mime];

        $pm     = PersistentManager::getInstance();
        $utente = $pm->load(Utente::class, Session::getUserId());
        if ($utente === null) { Session::logout(); return; }

        // Sposto il file nella cartella uploads/profili/
        $cartella = __DIR__ . '/../../uploads/profili';
        if (!is_dir($cartella)) {
            mkdir($cartella, 0777, true);
        }
        $nomeFile = 'profilo_' . $utente->getId() . '_' . uniqid() . '.' . $estensione;
        $percorsoAssoluto = $cartella . '/' . $nomeFile;

        if (!move_uploaded_file($file['tmp_name'], $percorsoAssoluto)) {
            Session::setFlash('errore', 'Impossibile salvare l\'immagine sul server.');
            header('Location: index.php?action=profilo');
            exit;
        }

        // Rimuovo la vecchia foto dal disco, se c'era.
        $vecchia = $utente->getFotoProfilo();
        if ($vecchia !== null && $vecchia !== '') {
            $vecchiaAssoluta = __DIR__ . '/../../' . $vecchia;
            if (is_file($vecchiaAssoluta)) {
                @unlink($vecchiaAssoluta);
            }
        }

        // Salvo il percorso relativo e aggiorno la sessione (per la sidebar).
        $percorsoRelativo = 'uploads/profili/' . $nomeFile;
        $utente->setFotoProfilo($percorsoRelativo);
        $pm->store($utente);
        Session::set('fotoProfilo', $percorsoRelativo);

        Session::setFlash('successo', 'Immagine del profilo aggiornata.');
        header('Location: index.php?action=profilo');
        exit;
    }
}
