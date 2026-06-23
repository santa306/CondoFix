<?php
// src/View/ViewListaLavoratori.php
//
// VIEW della lista Lavoratori (Amministratore).
// Solo OUTPUT: riceve dal Control admin + lavoratori e li passa al template.

class ViewListaLavoratori extends ViewBase
{
    /**
     * @param Amministratore|null $admin       l'admin loggato (sidebar)
     * @param array               $lavoratori  i fornitori (oggetti Fornitore)
     */
    public function mostra(?Amministratore $admin, array $lavoratori): void
    {
        $nomeCompleto = $admin
            ? $admin->getNome() . ' ' . $admin->getCognome()
            : 'Amministratore';

        $this->assign('titolo',       'CondoFix — Lavoratori');
        $this->assign('nomeCompleto', $nomeCompleto);
        $this->assign('lavoratori',   $lavoratori);

        $this->assign('successo', Session::getFlash('successo'));
        $this->assign('errore',   Session::getFlash('errore'));

        $this->render('lista_lavoratori.tpl');
    }
}
