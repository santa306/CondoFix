<?php
// src/View/ViewCreaCondominio.php
//
// VIEW del form "Nuovo condominio" (Amministratore).
//   - INPUT: legge nome, indirizzo, citta da $_POST (unico punto che tocca POST)
//   - OUTPUT: disegna crea_condominio_admin.tpl

class ViewCreaCondominio extends ViewBase
{
    // -------------------------------------------------------
    // INPUT
    // -------------------------------------------------------

    public function getNome(): string
    {
        return trim($this->post('nome'));
    }

    public function getIndirizzo(): string
    {
        return trim($this->post('indirizzo'));
    }

    public function getCitta(): string
    {
        return trim($this->post('citta'));
    }

    // -------------------------------------------------------
    // OUTPUT
    // -------------------------------------------------------

    public function mostraForm(): void
    {
        $this->assign('titolo',   'CondoFix — Nuovo condominio');
        $this->assign('errore',   Session::getFlash('errore'));
        $this->assign('successo', Session::getFlash('successo'));
        $this->render('crea_condominio_admin.tpl');
    }
}
