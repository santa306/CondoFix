<?php
// src/View/ViewDettaglioIntervento.php
//
// VIEW del dettaglio di un intervento.
//   - INPUT: legge l'id dalla query string (?id=NN).
//   - OUTPUT: disegna dettaglio_intervento.tpl con dati, stato, note e foto.

class ViewDettaglioIntervento extends ViewBase
{
    // -------------------------------------------------------
    // INPUT
    // -------------------------------------------------------
    public function getIdIntervento(): int
    {
        return (int) $this->get('id');
    }

    // -------------------------------------------------------
    // OUTPUT
    // -------------------------------------------------------
    /**
     * @param Intervento $intervento
     * @param Nota[]     $note
     * @param Foto[]     $foto
     */
    public function mostra(Intervento $intervento, array $note, array $foto): void
    {
        $this->assign('titolo',     'CondoFix — ' . $intervento->getTitolo());
        $this->assign('intervento', $intervento);
        $this->assign('stato',      $intervento->getStato());
        $this->assign('note',       $note);
        $this->assign('foto',       $foto);
        $this->assign('errore',     Session::getFlash('errore'));
        $this->assign('successo',   Session::getFlash('successo'));
        $this->render('dettaglio_intervento.tpl');
    }
}
