<?php
// src/View/ViewCaricaFoto.php
//
// VIEW di caricaFoto.
//
// Particolarita': i file caricati NON stanno in $_POST ma in $_FILES.
// Quindi qui c'e' un metodo apposito getFotoCaricata() che legge $_FILES.
// Resta valido il principio: la View e' l'unico punto che tocca l'input HTTP,
// il Control non legge mai $_FILES direttamente.

class ViewCaricaFoto extends ViewBase
{
    public function getIdIntervento(): int
    {
        return (int) $this->post('id');
    }

    /**
     * Ritorna l'array di $_FILES['foto'] (name, type, tmp_name, error, size)
     * oppure null se nessun file e' stato inviato.
     */
    public function getFotoCaricata(): ?array
    {
        return $_FILES['foto'] ?? null;
    }
}
