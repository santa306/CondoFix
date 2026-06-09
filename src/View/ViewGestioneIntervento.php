<?php
// src/View/ViewGestioneIntervento.php
//
// VIEW condivisa per la gestione di un singolo intervento da parte dell'admin.
// La usano TRE Control:
//   - CDettaglioIntervento -> chiama mostra() per disegnare la scheda
//   - CAccettaIntervento   -> legge getPriorita() e getIdFornitore() dal form APPROVA
//   - CNegaIntervento       -> legge getMotivazione() dal form NEGA
//
// È l'unico punto che tocca $_GET/$_POST per questa famiglia di operazioni
// (i metodi get*() ereditano gli helper get()/post() da ViewBase).

class ViewGestioneIntervento extends ViewBase
{
    // ---------- INPUT (gli unici punti che leggono $_GET / $_POST) ----------

    public function getIdIntervento(): int  { return (int) $this->get('id'); }

    // Dati del form APPROVA
    public function getPriorita(): string   { return (string) $this->post('priorita'); }
    public function getIdFornitore(): int   { return (int) $this->post('id_fornitore'); }

    // Dato del form NEGA
    public function getMotivazione(): string { return (string) $this->post('motivazione'); }

    // File del form ALLEGA FATTURA: ritorna l'elemento di $_FILES (array con
    // 'name','tmp_name','size','error',...) oppure null se non è stato inviato.
    // È l'unico punto che tocca $_FILES, coerente col fatto che la View è
    // l'unico strato che legge l'input HTTP.
    public function getFileFattura(): ?array
    {
        return $_FILES['fattura'] ?? null;
    }

    // ---------- OUTPUT ----------

    /**
     * Disegna la scheda di dettaglio dell'intervento.
     *
     * @param Intervento $intervento l'intervento da mostrare
     * @param array      $fornitori  fornitori selezionabili (per il form APPROVA)
     */
    public function mostra(Intervento $intervento, array $fornitori = []): void
    {
        $this->assign('titolo',     'Dettaglio intervento');
        $this->assign('intervento', $intervento);
        $this->assign('fornitori',  $fornitori);

        // true solo se l'intervento è ancora da valutare: in tal caso il
        // template mostra i pulsanti/form NEGA e APPROVA.
        $this->assign('daValutare', $intervento->getStato() instanceof Presentato);

        $this->assign('successo', Session::getFlash('successo'));
        $this->assign('errore',   Session::getFlash('errore'));

        $this->render('dettaglio_intervento.tpl');
    }
}
