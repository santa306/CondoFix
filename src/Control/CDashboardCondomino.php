<?php
// src/Control/CDashboardCondomino.php
//
// CONTROLLORE — operazione di sistema "Visualizza dashboard del condomino".
//
// RUOLO NELLO STRATO CONTROL (stesso schema di CLogin):
//   1. protegge la pagina con Session::requireRole('condomino')
//   2. ricava l'utente loggato dalla Foundation (via PersistentManager)
//   3. chiede alla Foundation i suoi interventi
//   4. calcola i contatori per le card della dashboard
//   5. passa i dati alla View, che disegna il template
//
//   NON tocca $_GET/$_POST (lo fa la View), NON conosce Doctrine
//   (passa sempre da PersistentManager), NON usa Smarty (lo fa la View).
//
// Le classi del progetto sono nel namespace globale: nessun "namespace" qui.

class CDashboardCondomino
{
    // -------------------------------------------------------
    // mostra() — disegna la dashboard del condomino loggato.
    //
    // Collegata in index.php a:  ?action=dashboardCondomino
    // -------------------------------------------------------
    public function mostra(): void
    {
        // 1. PERMESSI
        //    Solo un condomino loggato può vedere la propria dashboard.
        //    Se non è loggato o ha un altro ruolo, requireRole reindirizza/blocca.
        Session::requireRole('condomino');

        // 2. RICAVO L'UTENTE LOGGATO
        //    In sessione c'è solo l'id: ricarico l'oggetto Condomino dal DB
        //    tramite la Foundation (mai accesso diretto a Doctrine).
        $pm        = PersistentManager::getInstance();
        $idUtente  = Session::getUserId();
        $condomino = $pm->load(Condomino::class, $idUtente);

        // Difensivo: se per qualche motivo l'utente in sessione non esiste
        // più nel DB, chiudo la sessione e torno al login.
        if ($condomino === null) {
            Session::logout();   // fa già il redirect ed esce
            return;
        }

        // 3. CHIEDO ALLA FOUNDATION I SUOI INTERVENTI
        //    findBySegnalante restituisce già gli interventi ordinati per
        //    dataCreazione DESC (i più recenti per primi).
        $interventi = $pm->intervento()->findBySegnalante($condomino);

        // 4. CALCOLO I CONTATORI PER LE CARD
        //    Li calcolo qui (nel Control) scorrendo la lista UNA volta sola:
        //    così non faccio query extra e la View resta passiva.
        $contatori = $this->contaPerStato($interventi);

        // 5. PASSO TUTTO ALLA VIEW
        $view = new ViewDashboardCondomino();
        $view->mostra($condomino, $interventi, $contatori);
    }

    // =======================================================
    // HELPER PRIVATO — non è un'operazione di sistema, è logica
    // di supporto del Control per preparare i dati alla View.
    // =======================================================

    /**
     * Conta gli interventi raggruppandoli per tipo di stato.
     * Restituisce un array associativo pronto per le card della dashboard.
     *
     * @param Intervento[] $interventi
     * @return array{totali:int,presentato:int,accettato:int,in_corso:int,completato:int,negato:int}
     */
    private function contaPerStato(array $interventi): array
    {
        // Inizializzo tutti i contatori a zero così le card mostrano "0"
        // anche quando non ci sono interventi di quel tipo.
        $c = [
            'totali'     => 0,
            'presentato' => 0,
            'accettato'  => 0,   // card "Da Fare"
            'in_corso'   => 0,
            'completato' => 0,
            'negato'     => 0,
        ];

        foreach ($interventi as $intervento) {
            $c['totali']++;

            // getTipo() restituisce: 'presentato' | 'negato' | 'accettato'
            //                        | 'in_corso' | 'completato'
            $tipo = $intervento->getStato()?->getTipo();

            // Incremento solo se il tipo è uno di quelli che contiamo
            if ($tipo !== null && isset($c[$tipo])) {
                $c[$tipo]++;
            }
        }

        return $c;
    }
}
