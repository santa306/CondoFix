<?php
// src/View/ViewProfilo.php
//
// VIEW del profilo personale.
//   - INPUT: campi password (POST) e file immagine ($_FILES)
//   - OUTPUT: disegna profilo.tpl

class ViewProfilo extends ViewBase
{
    // INPUT — cambio password
    public function getAttuale(): string { return $this->post('attuale'); }
    public function getNuova(): string   { return $this->post('nuova'); }
    public function getNuova2(): string  { return $this->post('nuova2'); }

    // INPUT — file immagine caricato (o null se assente)
    public function getFotoCaricata(): ?array
    {
        return $_FILES['foto'] ?? null;
    }

    // OUTPUT
    public function mostra(Utente $utente): void
    {
        $this->assign('titolo',   'CondoFix — Il mio profilo');
        $this->assign('utente',   $utente);
        $this->assign('ruolo',    Session::getRuolo());
        $this->assign('successo', Session::getFlash('successo'));
        $this->assign('errore',   Session::getFlash('errore'));
        $this->render('profilo.tpl');
    }
}
