<?php
// src/View/ViewListaCondomini.php
//
// VIEW della lista Condomini (Amministratore).
// Solo OUTPUT: riceve dal Control admin + condomini e li passa al template.

class ViewListaCondomini extends ViewBase
{
    /**
     * @param Amministratore|null $admin      l'admin loggato (sidebar)
     * @param array               $condomini  i condomini (oggetti Condominio)
     */
    public function mostra(?Amministratore $admin, array $condomini): void
    {
        $nomeCompleto = $admin
            ? $admin->getNome() . ' ' . $admin->getCognome()
            : 'Amministratore';

        $this->assign('titolo',       'CondoFix — Condomini');
        $this->assign('nomeCompleto', $nomeCompleto);
        $this->assign('condomini',    $condomini);

        $this->assign('successo', Session::getFlash('successo'));
        $this->assign('errore',   Session::getFlash('errore'));

        // Banner di esito (riepilogo azione: crea condominio).
        $this->assign('banner', Session::getBanner());

        $this->render('lista_condomini.tpl');
    }
}
