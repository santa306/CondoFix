<?php
// src/Control/CDashboardAdmin.php
//
// CONTROLLORE — operazione di sistema "visualizza Dashboard Amministratore".
// ATTORE: Amministratore.
//
// Caso d'uso (sketch pag. 1, "Monitoraggio"):
//   Dopo il login l'admin vede la sua dashboard con:
//     - i contatori in tempo reale (card): totali, presentati, da fare,
//       in corso, completati, condomini gestiti, lavoratori
//     - l'elenco dei lavori recenti
//
// RUOLO NELLO STRATO CONTROL (identico allo schema di CLogin):
//   1. controllo permessi con Session
//   2. recupero i dati SOLO via PersistentManager (mai Doctrine qui)
//   3. passo i dati alla View, che li disegna col template Smarty
//
// NB: lascio intatto il vecchio CDashboard (lo usano le dashboard di
//     fornitore e condomino degli altri blocchi). Questa è la versione
//     "vera" della sola dashboard admin, collegata al case 'dashboardAdmin'.

class CDashboardAdmin
{
    public function mostra(): void
    {
        // 1. PERMESSI — solo l'amministratore loggato può vedere questa pagina.
        Session::requireRole('amministratore');

        // 2. DATI (solo tramite PersistentManager) -------------------------
        $pm = PersistentManager::getInstance();

        // 2a. L'amministratore loggato (mi serve per "i suoi condomini").
        //     L'id è in sessione; carico l'oggetto Entity corrispondente.
        $admin = $pm->load(Amministratore::class, Session::getUserId());

        // 2b. Interventi raggruppati per stato -> da qui ricavo i contatori.
        //     findGroupedByStato() torna un array con le chiavi:
        //     presentato | negato | accettato | in_corso | completato
        $perStato = $pm->intervento()->findGroupedByStato();

        // 2c. Contatori delle card (sketch: 7 card).
        //     "Da fare" = lavori accettati ma non ancora avviati.
        $contatori = [
            'totali'      => array_sum(array_map('count', $perStato)),
            'presentati'  => count($perStato['presentato']),
            'da_fare'     => count($perStato['accettato']),
            'in_corso'    => count($perStato['in_corso']),
            'completati'  => count($perStato['completato']),
            // Condomini gestiti DALL'admin loggato (relazione del dominio).
            'condomini'   => count($pm->condominio()->findByAmministratore($admin)),
            // Lavoratori: nel dominio i fornitori sono globali (assegnabili da
            // qualunque admin), non c'è un legame admin->fornitore. Quindi qui
            // mostro il totale dei fornitori del sistema. Scelta ragionevole e
            // facile da cambiare se in futuro si aggiunge la relazione.
            'lavoratori'  => count($pm->utente()->findAllFornitori()),
        ];

        // 2d. Lavori recenti: tutti gli interventi, dal più recente.
        $recenti = $pm->intervento()->findRecenti(5);

        // 3. PASSO TUTTO ALLA VIEW -----------------------------------------
        (new ViewDashboardAdmin())->mostra($admin, $contatori, $recenti);
    }
}
