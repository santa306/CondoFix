<?php
// src/View/ViewRegistrazione.php
//
// VIEW della registrazione Amministratore.
//   - INPUT: legge i campi del form da $_POST
//   - OUTPUT: disegna registrazione.tpl
// Nessuna logica di business: solo input (HTTP) e output (HTML).

class ViewRegistrazione extends ViewBase
{
    // -------------------------------------------------------
    // INPUT
    // -------------------------------------------------------

    public function getNome(): string      { return $this->post('nome'); }
    public function getCognome(): string   { return $this->post('cognome'); }
    public function getEmail(): string     { return $this->post('email'); }
    public function getTelefono(): string  { return $this->post('telefono'); }
    public function getPassword(): string  { return $this->post('password'); }
    public function getPassword2(): string { return $this->post('password2'); }

    // -------------------------------------------------------
    // OUTPUT
    // -------------------------------------------------------

    /**
     * Mostra il form di registrazione.
     * I valori "vecchi" (passati dal Control dopo un errore) ripopolano i
     * campi così l'utente non deve riscrivere tutto. Le password non si
     * ripopolano mai, per sicurezza.
     *
     * @param array $vecchi ['nome'=>..,'cognome'=>..,'email'=>..,'telefono'=>..]
     */
    public function mostraForm(array $vecchi = []): void
    {
        $this->assign('titolo',   'CondoFix — Registrazione');
        $this->assign('errore',   Session::getFlash('errore'));

        $this->assign('vNome',     $vecchi['nome']     ?? '');
        $this->assign('vCognome',  $vecchi['cognome']  ?? '');
        $this->assign('vEmail',    $vecchi['email']    ?? '');
        $this->assign('vTelefono', $vecchi['telefono'] ?? '');

        $this->render('registrazione.tpl');
    }
}
