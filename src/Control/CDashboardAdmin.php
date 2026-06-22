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

        // 2d. Lista lavori: se c'e' una ricerca per titolo mostro i risultati,
        //     altrimenti i lavori recenti. Il termine cercato lo legge la View
        //     (unico punto che tocca l'input HTTP).
        $view  = new ViewDashboardAdmin();
        $cerca = $view->getCerca();
        $stato = $view->getStato();

        if ($cerca !== '') {
            // ricerca per titolo: cerca in tutto il sistema
            $recenti = $pm->intervento()->cercaTutti($cerca);
        } elseif ($stato !== '') {
            // filtro per stato (click su una card): parto da TUTTI i lavori
            $recenti = $this->filtraPerStato($pm->intervento()->findRecenti(100000), $stato);
        } else {
            // vista normale: TUTTI i lavori del sistema (dal piu' recente).
            // Uso findRecenti con un limite alto per riusare il metodo esistente.
            $recenti = $pm->intervento()->findRecenti(100000);
        }

        // 3. PASSO TUTTO ALLA VIEW -----------------------------------------
        $view->mostra($admin, $contatori, $recenti, $cerca, $stato);
    }

    /**
     * Filtra una lista di interventi per stato (click su una card contatore).
     * @param Intervento[] $interventi
     * @return Intervento[]
     */
    private function filtraPerStato(array $interventi, string $stato): array
    {
        $out = [];
        foreach ($interventi as $i) {
            if ($i->getStato()?->getTipo() === $stato) {
                $out[] = $i;
            }
        }
        return $out;
    }
}
