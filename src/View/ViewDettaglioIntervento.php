<?php
// src/View/ViewDettaglioIntervento.php
//
// VIEW del dettaglio di un intervento (lato Fornitore).
//
// RESPONSABILITA':
//   - INPUT:  legge l'id dell'intervento da $_GET (unico punto che tocca HTTP)
//   - OUTPUT: prepara i dati e disegna dettaglio_intervento.tpl
//
// Riceve dal Control l'intervento, l'elenco note e l'elenco foto gia' pronti.

class ViewDettaglioIntervento extends ViewBase
{
    // -------------------------------------------------------
    // INPUT
    // -------------------------------------------------------

    public function getIdIntervento(): int
    {
        // L'id arriva via GET dal link "Dettaglio" della dashboard:
        // index.php?action=dettaglioIntervento&id=NN
        return (int) $this->get('id');
    }

    // -------------------------------------------------------
    // OUTPUT
    // -------------------------------------------------------

    /**
     * Disegna la pagina di dettaglio.
     *
     * @param Intervento $intervento  il lavoro da mostrare
     * @param array      $note        storico note operative (oggetti Nota)
     * @param array      $foto        foto allegate (oggetti Foto)
     */
    public function mostra(Intervento $intervento, array $note, array $foto): void
    {
        $this->assign('titolo', 'CondoFix — ' . $intervento->getTitolo());

        // L'intervento intero: il template legge titolo, descrizione, stato, ecc.
        $this->assign('intervento', $intervento);

        // Tipo di stato come stringa, per decidere badge e pulsante azione
        $this->assign('tipoStato', $intervento->getStato()->getTipo());

        // Storico note e galleria foto
        $this->assign('note',       $note);
        $this->assign('numeroNote', count($note));
        $this->assign('foto',       $foto);
        $this->assign('numeroFoto', count($foto));

        // Messaggi flash (esito di aggiungiNota / caricaFoto quando le faremo)
        $this->assign('errore',   Session::getFlash('errore'));
        $this->assign('successo', Session::getFlash('successo'));

        $this->render('dettaglio_intervento.tpl');
    }
}
