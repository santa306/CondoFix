<?php
// src/Foundation/Session.php
//
// RUOLO: gestisce lo stato della sessione HTTP per tutta l'applicazione.
//        Incapsula $_SESSION così i Control non usano mai direttamente
//        le funzioni PHP di sessione.
//
// PATTERN: classe a metodi statici (approccio "tutto statico" indicato
//          nelle slide come alternativa al Singleton).
//
// COME SI USA (dal Control):
//
//   // Login riuscito:
//   Session::set('userId', $utente->getId());
//   Session::set('ruolo',  'amministratore');
//
//   // Pagina protetta:
//   Session::requireAuth();          // redirect a login se non loggato
//   Session::requireRole('admin');   // redirect se ruolo sbagliato
//
//   // Leggere dati:
//   $id = Session::get('userId');
//
//   // Logout:
//   Session::destroy();

class Session
{
    // -------------------------------------------------------
    // AVVIO SESSIONE
    // -------------------------------------------------------

    /**
     * Avvia la sessione PHP se non è ancora stata avviata.
     * Viene chiamata automaticamente dagli altri metodi.
     */
    public static function start(): void
    {
        // Evita il warning "session already started"
        if (session_status() === PHP_SESSION_NONE) {
            // Impostazioni di sicurezza prima di avviare
            ini_set('session.cookie_httponly', '1');   // JS non può leggere il cookie
            ini_set('session.cookie_samesite', 'Lax'); // protezione CSRF base
            session_start();
        }
    }

    // -------------------------------------------------------
    // OPERAZIONI SU $_SESSION
    // -------------------------------------------------------

    /**
     * Salva un valore in sessione.
     * Uso: Session::set('userId', 42);
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Legge un valore dalla sessione.
     * Restituisce null se la chiave non esiste.
     * Uso: $id = Session::get('userId');
     */
    public static function get(string $key): mixed
    {
        self::start();
        return $_SESSION[$key] ?? null;
    }

    /**
     * Verifica se una chiave esiste in sessione.
     * Uso: if (Session::exists('userId')) { ... }
     */
    public static function exists(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Rimuove una singola variabile dalla sessione.
     * Uso: Session::remove('messaggio');
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Distrugge completamente la sessione (logout).
     * Uso: Session::destroy();
     */
    public static function destroy(): void
    {
        self::start();
        // Svuota l'array $_SESSION
        $_SESSION = [];
        // Elimina il cookie di sessione dal browser
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    // -------------------------------------------------------
    // MESSAGGI FLASH
    // Messaggi che vivono solo per una richiesta HTTP (es. "Salvato!")
    // -------------------------------------------------------

    /**
     * Imposta un messaggio flash (sparisce dopo la prima lettura).
     * Uso: Session::setFlash('successo', 'Intervento creato!');
     */
    public static function setFlash(string $tipo, string $messaggio): void
    {
        self::start();
        $_SESSION['_flash'][$tipo] = $messaggio;
    }

    /**
     * Legge e rimuove un messaggio flash.
     * Restituisce null se non esiste.
     * Uso: $msg = Session::getFlash('successo');
     */
    public static function getFlash(string $tipo): ?string
    {
        self::start();
        $msg = $_SESSION['_flash'][$tipo] ?? null;
        unset($_SESSION['_flash'][$tipo]);
        return $msg;
    }

    /**
     * Verifica se esiste un messaggio flash di un dato tipo.
     * Uso: if (Session::hasFlash('errore')) { ... }
     */
    public static function hasFlash(string $tipo): bool
    {
        self::start();
        return isset($_SESSION['_flash'][$tipo]);
    }

    // -------------------------------------------------------
    // BANNER DI ESITO (modale di conferma con riepilogo dati)
    // -------------------------------------------------------

    /**
     * Imposta un banner di esito da mostrare sulla pagina successiva.
     * A differenza del flash semplice (solo testo), il banner porta un array
     * di dati strutturati: tipo grafico (successo/errore), titolo, e righe di
     * riepilogo. Sparisce dopo la prima lettura, come il flash.
     *
     * Uso:
     *   Session::setBanner([
     *       'tipo'    => 'successo',          // 'successo' (verde) | 'errore' (rosso)
     *       'titolo'  => 'Intervento creato',
     *       'righe'   => ['Titolo' => '...', 'Priorità' => '...'],
     *   ]);
     */
    public static function setBanner(array $dati): void
    {
        self::start();
        $_SESSION['_banner'] = $dati;
    }

    /**
     * Legge e rimuove il banner di esito.
     * Restituisce null se non esiste.
     */
    public static function getBanner(): ?array
    {
        self::start();
        $banner = $_SESSION['_banner'] ?? null;
        unset($_SESSION['_banner']);
        return $banner;
    }

    // -------------------------------------------------------
    // PROTEZIONE DELLE PAGINE
    // -------------------------------------------------------

    /**
     * Verifica che l'utente sia loggato.
     * Se non lo è, lo reindirizza al login e termina l'esecuzione.
     *
     * Da mettere ALL'INIZIO di ogni pagina protetta.
     * Uso: Session::requireAuth();
     */
    public static function requireAuth(): void
    {
        self::start();
        if (!isset($_SESSION['userId'])) {
            header('Location: index.php?action=login');
            exit;
        }
        // Protezione anti-session hijacking:
        // verifica che l'IP del client non sia cambiato durante la sessione
        if (isset($_SESSION['loginIP']) &&
            $_SESSION['loginIP'] !== $_SERVER['REMOTE_ADDR']) {
            self::destroy();
            header('Location: index.php?action=login');
            exit;
        }
    }

    /**
     * Verifica che l'utente abbia un ruolo specifico.
     * Chiama requireAuth() automaticamente prima del controllo ruolo.
     *
     * Uso: Session::requireRole('amministratore');
     *      Session::requireRole('fornitore');
     *      Session::requireRole('condomino');
     */
    public static function requireRole(string $ruolo): void
    {
        self::requireAuth();
        if (self::get('ruolo') !== $ruolo) {
            http_response_code(403); echo 'Accesso negato.'; exit;
            exit;
        }
    }

    /**
     * Verifica che l'utente abbia uno dei ruoli consentiti.
     * Utile per pagine accessibili a più ruoli.
     *
     * Uso: Session::requireAnyRole(['amministratore', 'condomino']);
     */
    public static function requireAnyRole(array $ruoli): void
    {
        self::requireAuth();
        if (!in_array(self::get('ruolo'), $ruoli, true)) {
            http_response_code(403); echo 'Accesso negato.'; exit;
            exit;
        }
    }

    /**
     * Esegue il login: salva userId, ruolo e IP in sessione.
     * Da chiamare dal Control dopo aver verificato le credenziali.
     *
     * Uso: Session::login($utente->getId(), 'amministratore');
     */
    public static function login(int $userId, string $ruolo, string $nome = '', string $cognome = ''): void
    {
        self::start();
        // Rigenera l'ID di sessione per prevenire session fixation
        session_regenerate_id(true);
        $_SESSION['userId']  = $userId;
        $_SESSION['ruolo']   = $ruolo;
        $_SESSION['nome']    = $nome;
        $_SESSION['cognome'] = $cognome;
        $_SESSION['loginIP'] = $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Esegue il logout: distrugge la sessione e reindirizza al login.
     * Uso: Session::logout();
     */
    public static function logout(): void
    {
        self::destroy();
        header('Location: index.php?action=login');
        exit;
    }

    // -------------------------------------------------------
    // HELPER — DATI UTENTE CORRENTE
    // -------------------------------------------------------

    /**
     * Restituisce l'ID dell'utente loggato, oppure null.
     * Uso: $id = Session::getUserId();
     */
    public static function getUserId(): ?int
    {
        return self::get('userId');
    }

    /**
     * Restituisce il ruolo dell'utente loggato, oppure null.
     * Uso: $ruolo = Session::getRuolo();
     */
    public static function getRuolo(): ?string
    {
        return self::get('ruolo');
    }

    /**
     * Nome completo dell'utente loggato (per la sidebar/saluti).
     * Uso: $nome = Session::getNomeCompleto();
     */
    public static function getNomeCompleto(): string
    {
        $nome    = (string) self::get('nome');
        $cognome = (string) self::get('cognome');
        $completo = trim($nome . ' ' . $cognome);
        return $completo !== '' ? $completo : 'Utente';
    }

    /**
     * Etichetta leggibile del ruolo (per la sidebar).
     * Uso: $label = Session::getRuoloLabel();  // "Amministratore" / "Condomino" / "Lavoratore"
     */
    public static function getRuoloLabel(): string
    {
        switch (self::get('ruolo')) {
            case 'amministratore': return 'Amministratore';
            case 'condomino':      return 'Condomino';
            case 'fornitore':      return 'Lavoratore';
            default:               return '';
        }
    }

    /**
     * Restituisce true se l'utente è loggato.
     * Uso: if (Session::isLoggedIn()) { ... }
     */
    public static function isLoggedIn(): bool
    {
        self::start();
        return isset($_SESSION['userId']);
    }
}
