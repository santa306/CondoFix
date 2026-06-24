<?php
// src/View/ViewCreaLavoratore.php
//
// VIEW del form "Crea lavoratore".
//   - INPUT: legge i campi da $_POST
//   - OUTPUT: disegna crea_lavoratore.tpl

class ViewCreaLavoratore extends ViewBase
{
    // INPUT
    public function getNome(): string          { return $this->post('nome'); }
    public function getCognome(): string       { return $this->post('cognome'); }
    public function getEmail(): string         { return $this->post('email'); }
    public function getTelefono(): string      { return $this->post('telefono'); }
    public function getPartitaIva(): string    { return $this->post('partitaIva'); }
    public function getPassword(): string       { return $this->post('password'); }
    public function getCategoriaId(): string    { return $this->post('categoria'); }
    public function getNuovaCategoria(): string { return $this->post('nuovaCategoria'); }

    // OUTPUT
    public function mostraForm(array $categorie): void
    {
        $this->assign('titolo',    'CondoFix — Nuovo lavoratore');
        $this->assign('categorie', $categorie);
        $this->assign('errore',    Session::getFlash('errore'));
        $this->render('crea_lavoratore.tpl');
    }
}
