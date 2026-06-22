<?php
// src/View/ViewVetrinaPubblica.php
//
// VIEW della vetrina pubblica (Utente non registrato).
//
// Mostra lavori DIMOSTRATIVI (dati fissi passati dal Control, non dal DB):
//   - mostra()          -> elenco lavori demo (vetrina_pubblica.tpl)
//   - mostraDettaglio() -> singolo lavoro demo (vetrina_dettaglio.tpl)
//
// I lavori sono array associativi (titolo, descrizione, condominio, stato,
// data, id), non oggetti Intervento: sono dati di esempio, non entita'.

class ViewVetrinaPubblica extends ViewBase
{
    // INPUT: id del lavoro demo da mostrare (da GET).
    public function getId(): int
    {
        return (int) $this->get('id');
    }

    /**
     * Elenco dei lavori demo.
     * @param array $lavori  lista di lavori (array associativi)
     */
    public function mostra(array $lavori): void
    {
        $this->assign('titolo', 'CondoFix — I lavori');
        $this->assign('lavori', $lavori);
        $this->render('vetrina_pubblica.tpl');
    }

    /**
     * Dettaglio di un singolo lavoro demo.
     * @param array $lavoro  un lavoro (array associativo)
     */
    public function mostraDettaglio(array $lavoro): void
    {
        $this->assign('titolo', 'CondoFix — ' . $lavoro['titolo']);
        $this->assign('lavoro', $lavoro);
        $this->render('vetrina_dettaglio.tpl');
    }
}
