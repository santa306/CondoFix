<?php
// src/View/ViewAggiungiNota.php
//
// VIEW di aggiungiNota.
//
// Verticale azione + redirect: non disegna una pagina propria (il form vive
// dentro dettaglio_intervento.tpl). La View resta comunque l'unico punto
// autorizzato a leggere l'input HTTP: id dell'intervento e testo della nota,
// entrambi via POST dal form nel dettaglio.

class ViewAggiungiNota extends ViewBase
{
    public function getIdIntervento(): int
    {
        return (int) $this->post('id');
    }

    public function getTesto(): string
    {
        return (string) $this->post('testo');
    }
}
