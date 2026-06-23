<?php
// src/View/ViewAvviaIntervento.php
//
// VIEW di avviaIntervento.
//
// Questa verticale non disegna nessuna pagina (e' azione + redirect),
// ma la View esiste comunque: e' l'UNICO punto autorizzato a leggere
// l'input HTTP. Il Control non tocca mai $_POST direttamente.
//
// L'id arriva via POST dall'input hidden dei pulsanti "Inizia lavoro"
// (dashboard_fornitore.tpl e dettaglio_intervento.tpl).

class ViewAvviaIntervento extends ViewBase
{
    public function getIdIntervento(): int
    {
        return (int) $this->post('id');
    }
}
