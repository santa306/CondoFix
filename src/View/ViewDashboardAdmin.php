<?php
// src/View/ViewDashboardAdmin.php
//
// VIEW della Dashboard Amministratore.
//
// RESPONSABILITA' (strato Presentation):
//   - riceve dal Control i dati già pronti (admin, contatori, recenti)
//   - li passa al template Smarty dashboard_admin.tpl che disegna l'HTML
//
//   NON parla col database, NON contiene logica di business.
//   Estende ViewBase (che configura Smarty una volta sola).
//
// Questa View è di solo OUTPUT: la dashboard non legge input dall'utente,
// quindi non ha metodi get*() come ViewLogin. È normale.

class ViewDashboardAdmin extends ViewBase
{
    /**
     * Disegna la dashboard.
     *
     * @param Amministratore|null $admin      l'admin loggato (per nome/cognome)
     * @param array               $contatori  i 7 contatori delle card
     * @param array               $recenti    gli interventi recenti (oggetti Intervento)
     */
    public function mostra(?Amministratore $admin, array $contatori, array $recenti): void
    {
        // Nome da mostrare nel saluto e nella sidebar.
        $nomeCompleto = $admin
            ? $admin->getNome() . ' ' . $admin->getCognome()
            : 'Amministratore';

        $this->assign('titolo',       'CondoFix — Dashboard Amministratore');
        $this->assign('nomeCompleto', $nomeCompleto);
        $this->assign('contatori',    $contatori);
        $this->assign('recenti',      $recenti);

        // Messaggi flash lasciati dal login o da un'altra operazione.
        $this->assign('successo', Session::getFlash('successo'));
        $this->assign('errore',   Session::getFlash('errore'));

        $this->render('dashboard_admin.tpl');
    }
}
