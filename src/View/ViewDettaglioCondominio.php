<?php
// src/View/ViewDettaglioCondominio.php
//
// VIEW del dettaglio di un condominio (dati + lista condòmini).
// Solo OUTPUT.

class ViewDettaglioCondominio extends ViewBase
{
    public function mostra(?Amministratore $admin, Condominio $condominio, array $condomini): void
    {
        $nomeCompleto = $admin
            ? $admin->getNome() . ' ' . $admin->getCognome()
            : 'Amministratore';

        $this->assign('titolo',       'CondoFix — ' . $condominio->getNome());
        $this->assign('nomeCompleto', $nomeCompleto);
        $this->assign('condominio',   $condominio);
        $this->assign('condomini',    $condomini);

        $this->assign('successo', Session::getFlash('successo'));
        $this->assign('errore',   Session::getFlash('errore'));

        // Banner: scheda info condòmino o conferma creazione.
        $this->assign('banner', Session::getBanner());

        $this->render('dettaglio_condominio.tpl');
    }
}
