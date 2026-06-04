<?php
// src/View/ViewDashboardCondomino.php
//
// VIEW della dashboard del Condomino.
//
// RESPONSABILITA' (strato Presentation), come ViewLogin:
//   - OUTPUT: ricevere i dati già pronti dal Control e disegnarli
//     tramite il template Smarty dashboard_condomino.tpl.
//   (Qui non c'è INPUT da $_POST/$_GET perché la dashboard è una sola GET
//    senza parametri; i metodi getXxx() compaiono nelle View che leggono form.)
//
//   NON contiene logica di business, NON parla col database, NON calcola
//   i contatori: tutto ciò lo fa il Control. La View si limita ad assegnare
//   le variabili al template e a renderlo.

class ViewDashboardCondomino extends ViewBase
{
    // -------------------------------------------------------
    // OUTPUT — disegna la dashboard.
    //
    // @param Condomino    $condomino   utente loggato (per nome/cognome)
    // @param Intervento[] $interventi  i suoi interventi (già ordinati DESC)
    // @param array        $contatori   conteggi per stato (dal Control)
    // -------------------------------------------------------
    public function mostra(Condomino $condomino, array $interventi, array $contatori): void
    {
        // Titolo della pagina (come fa ViewLogin)
        $this->assign('titolo', 'CondoFix — Dashboard');

        // Dati dell'utente per il saluto e la sidebar
        $this->assign('nome',    $condomino->getNome());
        $this->assign('cognome', $condomino->getCognome());

        // Lista interventi e contatori (li passa il Control già pronti)
        $this->assign('interventi', $interventi);
        $this->assign('contatori',  $contatori);

        // Messaggio flash di successo (es. dopo aver creato una segnalazione)
        $this->assign('successo', Session::getFlash('successo'));

        // Disegna il template
        $this->render('dashboard_condomino.tpl');
    }
}
