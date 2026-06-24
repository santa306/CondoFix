<?php
// src/View/ViewLanding.php
//
// VIEW della landing page. Solo OUTPUT: nessun dato dal DB, è una pagina
// statica di presentazione.

class ViewLanding extends ViewBase
{
    public function mostra(): void
    {
        $this->assign('titolo', 'CondoFix — Gestione interventi condominiali');
        $this->render('landing.tpl');
    }
}
