<?php
// src/Control/CLogin.php
//
// CONTROLLORE — operazione di sistema "Login / Logout".
//
// RUOLO NELLO STRATO CONTROL:
//   Coordina il flusso  UI(View) → Control → Foundation → Control → UI.
//   NON conosce Doctrine, NON legge $_POST/$_GET direttamente, NON usa Smarty:
//     - i dati di input arrivano dalla View  (ViewLogin)
//     - la persistenza passa SOLO da PersistentManager
//     - lo stato di sessione passa SOLO da Session
//   Questa è la regola architetturale che separa Presentation / Control /
//   Foundation.
//
// SCHEMA DI OGNI METODO CONTROL (lo stesso del controllore di esempio):
//   1. istanzio la View e leggo da lei l'input
//   2. valido l'input
//   3. (eventuale) controllo permessi con Session
//   4. parlo con la Foundation via PersistentManager
//   5. restituisco il risultato alla View
//
// Le classi Entity / Foundation / Session del progetto sono nel namespace
// globale (nessun "namespace" dichiarato), quindi anche i Control restano
// nel namespace globale per coerenza: niente backslash, niente "use".

class CLogin
{
    // -------------------------------------------------------
    // mostraForm() — operazione di sistema: "richiedi login"
    //
    // Mostra la pagina con il form di login.
    // È il GET iniziale: nessun dato da validare, nessun DB.
    // -------------------------------------------------------
    public function mostraForm(): void
    {
        $view = new ViewLogin();

        // Se c'è un messaggio di errore lasciato da un tentativo precedente
        // (flash message), la View lo mostrerà. Il Control si limita a dire
        // alla View di disegnare il form.
        $view->mostraForm();
    }

    // -------------------------------------------------------
    // esegui() — operazione di sistema: "login(email, password)"
    //
    // Verifica le credenziali e, se valide, apre la sessione e reindirizza
    // l'utente alla dashboard del suo ruolo.
    // -------------------------------------------------------
    public function esegui(): void
    {
        // 1. ISTANZIO LA VIEW E LEGGO L'INPUT
        //    La View incapsula $_POST: il Control non lo tocca mai.
        $view     = new ViewLogin();
        $email    = $view->getEmail();
        $password = $view->getPassword();

        // 2. VALIDAZIONE
        //    Se l'input è inutilizzabile torno al form con un messaggio.
        if (empty(trim($email)) || empty($password)) {
            Session::setFlash('errore', 'Inserisci email e password.');
            $view->mostraForm();
            return;
        }

        // 3. (niente controllo ruolo qui: il login è la porta d'ingresso,
        //     è accessibile anche all'utente non registrato/non loggato)

        // 4. PARLO CON LA FOUNDATION
        //    Solo tramite PersistentManager. login() restituisce l'Utente
        //    se le credenziali sono valide, altrimenti null.
        $pm     = PersistentManager::getInstance();
        $utente = $pm->utente()->login($email, $password);

        if ($utente === null) {
            // Credenziali errate: messaggio + ridisegno il form.
            Session::setFlash('errore', 'Email o password non corretti.');
            $view->mostraForm();
            return;
        }

        // 4b. RICAVO IL RUOLO dall'oggetto Utente.
        //     Utente è SINGLE_TABLE: la sottoclasse concreta determina il ruolo.
        //     (Usiamo instanceof perché è il modo pulito di leggere il tipo
        //      senza esporre il discriminator Doctrine al Control.)
        $ruolo = $this->ricavaRuolo($utente);

        // 5. APRO LA SESSIONE e reindirizzo
        //    Session::login() rigenera l'id di sessione (anti session-fixation)
        //    e salva userId + ruolo + IP.
        Session::login($utente->getId(), $ruolo, $utente->getNome(), $utente->getCognome());
        Session::setFlash('successo', 'Benvenuto, ' . $utente->getNome() . '!');

        // Reindirizzo alla dashboard giusta. Il redirect lo fa il Control
        // perché è logica di flusso applicativo, non di presentazione.
        $this->redirectDashboard($ruolo);
    }

    // -------------------------------------------------------
    // logout() — operazione di sistema: "logout()"
    // -------------------------------------------------------
    public function logout(): void
    {
        // Session::logout() distrugge la sessione e fa il redirect al login.
        Session::logout();
    }

    // =======================================================
    // HELPER PRIVATI (logica di supporto del Control, non operazioni SSD)
    // =======================================================

    /**
     * Traduce la sottoclasse concreta di Utente nella stringa di ruolo
     * usata da Session (coerente con i valori passati a requireRole()).
     */
    private function ricavaRuolo(Utente $utente): string
    {
        if ($utente instanceof Amministratore) return 'amministratore';
        if ($utente instanceof Fornitore)      return 'fornitore';
        if ($utente instanceof Condomino)      return 'condomino';

        // Non dovrebbe accadere: ogni utente è una delle tre sottoclassi.
        return 'sconosciuto';
    }

    /**
     * Reindirizza alla dashboard corretta in base al ruolo.
     * Usa il routing index.php?action=... previsto dal progetto.
     */
    private function redirectDashboard(string $ruolo): void
    {
        $action = match ($ruolo) {
            'amministratore' => 'dashboardAdmin',
            'fornitore'      => 'dashboardFornitore',
            'condomino'      => 'dashboardCondomino',
            default          => 'login',
        };
        header('Location: index.php?action=' . $action);
        exit;
    }
}
