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
    // INPUT: filtro per stato (da GET, opzionale; click su una card contatore).
    public function getStato(): string
    {
        return trim($this->get('stato'));
    }

    // INPUT: filtro per categoria del fornitore (id, da GET, opzionale).
    public function getCategoria(): string
    {
        return trim($this->get('categoria'));
    }

    // INPUT: filtro per condominio (id, da GET, opzionale).
    public function getCondominio(): string
    {
        return trim($this->get('condominio'));
    }

    public function mostra(?Amministratore $admin, array $contatori, array $recenti, string $stato = '', string $categoria = '', string $condominio = '', array $categorie = [], array $condomini = []): void
    {
        // Nome da mostrare nel saluto e nella sidebar.
        $nomeCompleto = $admin
            ? $admin->getNome() . ' ' . $admin->getCognome()
            : 'Amministratore';

        $this->assign('titolo',       'CondoFix — Dashboard Amministratore');
        $this->assign('nomeCompleto', $nomeCompleto);
        $this->assign('contatori',    $contatori);
        $this->assign('recenti',      $recenti);
        $this->assign('stato',        $stato);
        // Filtri attivi e liste per i menu a tendina.
        $this->assign('filtroCategoria',  $categoria);
        $this->assign('filtroCondominio', $condominio);
        $this->assign('categorie',        $categorie);
        $this->assign('condomini',        $condomini);

        // Messaggi flash lasciati dal login o da un'altra operazione.
        $this->assign('successo', Session::getFlash('successo'));
        $this->assign('errore',   Session::getFlash('errore'));

        // Banner di esito (riepilogo azione: crea lavoro / accetta / nega).
        $this->assign('banner', Session::getBanner());

        $this->render('dashboard_admin.tpl');
    }
}
