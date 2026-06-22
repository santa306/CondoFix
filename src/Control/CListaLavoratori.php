<?php
// src/Control/CListaLavoratori.php
//
// CONTROLLORE — "visualizza lista Lavoratori" (Amministratore).
// ATTORE: Amministratore.
//
// Mostra l'elenco di tutti i fornitori (lavoratori) registrati nel sistema
// con i loro dati. Stesso schema degli altri Control:
//   1. controllo permessi con Session
//   2. recupero dati SOLO via PersistentManager
//   3. passo i dati alla View

class CListaLavoratori
{
    public function mostra(): void
    {
        Session::requireRole('amministratore');

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) {
            Session::logout();
            return;
        }

        // Tutti i fornitori del sistema (i "lavoratori").
        $lavoratori = $pm->utente()->findAllFornitori();

        (new ViewListaLavoratori())->mostra($admin, $lavoratori);
    }
}
