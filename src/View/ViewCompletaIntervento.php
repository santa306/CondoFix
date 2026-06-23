<?php
// src/View/ViewCompletaIntervento.php
//
// VIEW di completaIntervento.
//
// Come ViewAvviaIntervento: nessuna pagina da disegnare (azione + redirect),
// solo lettura dell'input HTTP. L'id arriva via POST dall'input hidden del
// pulsante "Completa lavoro" (dashboard_fornitore.tpl e dettaglio_intervento.tpl).

class ViewCompletaIntervento extends ViewBase
{
    public function getIdIntervento(): int
    {
        return (int) $this->post('id');
    }
}
