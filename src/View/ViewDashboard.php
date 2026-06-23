<?php
// src/View/ViewDashboard.php
//
// VIEW placeholder delle dashboard. Mostra solo un saluto e il logout,
// per dimostrare che il login + sessione + redirect funzionano end-to-end.
// La rimpiazzerai/espanderai con i contenuti reali di ogni ruolo.

class ViewDashboard extends ViewBase
{
    /**
     * @param string $ruoloLabel etichetta leggibile del ruolo (es. "Amministratore")
     */
    public function mostra(string $ruoloLabel): void
    {
        $this->assign('titolo',     'CondoFix — Dashboard ' . $ruoloLabel);
        $this->assign('ruoloLabel', $ruoloLabel);
        $this->assign('successo',   Session::getFlash('successo'));
        $this->render('dashboard.tpl');
    }
}
