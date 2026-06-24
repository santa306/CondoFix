<?php
// src/View/ViewCambioPassword.php
//
// VIEW del cambio password obbligatorio al primo accesso.
//   - INPUT: legge attuale / nuova / nuova2 da $_POST
//   - OUTPUT: disegna cambio_password.tpl

class ViewCambioPassword extends ViewBase
{
    // INPUT
    public function getAttuale(): string { return $this->post('attuale'); }
    public function getNuova(): string   { return $this->post('nuova'); }
    public function getNuova2(): string  { return $this->post('nuova2'); }

    // OUTPUT
    public function mostraForm(): void
    {
        $this->assign('titolo', 'CondoFix — Cambio password');
        $this->assign('errore', Session::getFlash('errore'));
        $this->render('cambio_password.tpl');
    }
}
