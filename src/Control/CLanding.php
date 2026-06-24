<?php
// src/Control/CLanding.php
//
// CONTROLLORE — pagina d'ingresso (landing) di CondoFix.
// Pagina pubblica: nessun controllo di ruolo, è la porta del sito.
// Mostra la presentazione con i pulsanti Accedi / Registrati / Inizia gratis.

class CLanding
{
    public function mostra(): void
    {
        (new ViewLanding())->mostra();
    }
}
