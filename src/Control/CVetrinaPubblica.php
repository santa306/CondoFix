<?php
// src/Control/CVetrinaPubblica.php
//
// CONTROLLORE — operazione di sistema "Vetrina pubblica".
// ATTORE: Utente non registrato (visitatore non loggato).
//
// Mostra a CHIUNQUE, senza login, l'elenco dei lavori del sistema in
// sola lettura. NON richiede autenticazione (e' l'unica pagina senza
// Session::requireRole). Per questo motivo la View mostra solo dati non
// operativi: titolo, descrizione, condominio, stato e data. NON mostra
// segnalante, fornitore, note o foto.
//
// NB sui dati: nel progetto d'esame gli interventi sono dati di PROVA
// (titoli tipo "prova lampadina"), quindi mostrarli pubblicamente non
// espone informazioni personali reali. In un sistema di produzione si
// filtrerebbero ulteriormente i campi sensibili (descrizione, indirizzo).

class CVetrinaPubblica
{
    public function mostra(): void
    {
        // NESSUN controllo di permessi: la pagina e' pubblica.

        // Carico tutti i lavori del sistema, dal piu' recente.
        // Riuso findRecenti con un limite alto per avere "tutti".
        $pm     = PersistentManager::getInstance();
        $lavori = $pm->intervento()->findRecenti(100000);

        // Passo i dati alla View pubblica.
        (new ViewVetrinaPubblica())->mostra($lavori);
    }
}
