<?php
// src/View/ViewLogin.php
//
// VIEW della pagina di Login.
//
// RESPONSABILITA' (strato Presentation):
//   - LEGGERE l'input dell'utente da $_POST  (getEmail / getPassword)
//   - DISEGNARE il form di login tramite il template Smarty login.tpl
//
//   NON contiene logica di business, NON parla con il database:
//   si limita a input (da HTTP) e output (HTML via Smarty).
//   Il Control (CLogin) la usa cosi':
//       $view = new ViewLogin();
//       $email = $view->getEmail();      // input
//       $view->mostraForm();             // output

class ViewLogin extends ViewBase
{
    // -------------------------------------------------------
    // INPUT — letti da $_POST (incapsula HTTP per il Control)
    // -------------------------------------------------------

    public function getEmail(): string
    {
        return $this->post('email');
    }

    public function getPassword(): string
    {
        // La password NON va trim-mata negli spazi interni, ma post() fa trim
        // solo dei bordi: per una password va bene. Se volessi essere
        // rigorosissimo leggeresti $_POST['password'] grezzo; qui teniamo
        // semplice e coerente con le altre View.
        return $this->post('password');
    }

    // -------------------------------------------------------
    // OUTPUT — disegna il form di login
    // -------------------------------------------------------

    /**
     * Mostra la pagina di login.
     * Recupera l'eventuale messaggio flash (errore/successo) lasciato dal
     * Control e lo passa al template, poi rende login.tpl.
     */
    public function mostraForm(): void
    {
        // Messaggi flash: il Control li imposta con Session::setFlash(...),
        // la View li legge e li passa al template. getFlash li consuma
        // (spariscono dopo la prima visualizzazione).
        $this->assign('errore',   Session::getFlash('errore'));
        $this->assign('successo', Session::getFlash('successo'));

        // Titolo della pagina (esempio di dato passato al template)
        $this->assign('titolo', 'CondoFix — Accesso');

        // Disegna il template
        $this->render('login.tpl');
    }
}
