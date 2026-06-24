<?php
// src/View/ViewCreaCondomino.php
//
// VIEW del form "Aggiungi condòmino".
//   - INPUT: legge i campi da $_POST
//   - OUTPUT: disegna crea_condomino.tpl

class ViewCreaCondomino extends ViewBase
{
    // INPUT
    public function getNome(): string     { return $this->post('nome'); }
    public function getCognome(): string  { return $this->post('cognome'); }
    public function getEmail(): string    { return $this->post('email'); }
    public function getInterno(): string  { return $this->post('interno'); }
    public function getPassword(): string { return $this->post('password'); }

    // OUTPUT
    public function mostraForm(Condominio $condominio): void
    {
        $this->assign('titolo',     'CondoFix — Nuovo condòmino');
        $this->assign('condominio', $condominio);
        $this->assign('errore',     Session::getFlash('errore'));
        $this->render('crea_condomino.tpl');
    }
}
