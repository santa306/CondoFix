<?php
// src/View/ViewDashboardFornitore.php
//
// VIEW della dashboard del Fornitore ("I miei lavori").
//
// RESPONSABILITA' (strato Presentation):
//   - OUTPUT: prepara i dati per il template e disegna dashboard_fornitore.tpl
//   In questa verticale non c'e' input da $_POST/$_GET: e' una pagina di sola
//   lettura. Gli input (id intervento, ecc.) arriveranno nelle verticali
//   successive (avviaIntervento, dettaglioIntervento, ...).
//
// NON contiene logica di business e NON parla col DB: riceve gia' pronti
// dal Control il fornitore e l'elenco dei suoi lavori.

class ViewDashboardFornitore extends ViewBase
{
    /**
     * Disegna la pagina "I miei lavori".
     *
     * @param Fornitore $fornitore  l'utente loggato (per il saluto)
     * @param array     $lavori     interventi attivi assegnati a lui
     */
    public function mostra(Fornitore $fornitore, array $lavori): void
    {
        $this->assign('titolo', 'CondoFix — I miei lavori');

        // Nome completo per il saluto "Ciao, Nome Cognome"
        $this->assign('nomeCompleto', $fornitore->getNome() . ' ' . $fornitore->getCognome());

        // Elenco dei lavori e quanti sono (lo sketch mostra "Hai N lavori attivi")
        $this->assign('lavori',       $lavori);
        $this->assign('numeroLavori', count($lavori));

        // Messaggi flash lasciati dai Control delle altre verticali
        // (es. dopo "Inizia lavoro" o "Completa lavoro").
        $this->assign('errore',   Session::getFlash('errore'));
        $this->assign('successo', Session::getFlash('successo'));

        $this->render('dashboard_fornitore.tpl');
    }
}
