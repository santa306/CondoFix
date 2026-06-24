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

        // 3. RICAVO IL CONDOMINIO del condomino loggato.
        //    Il condomino vede TUTTI i lavori del proprio condominio (in ogni
        //    stato), anche quelli segnalati da altri condomini del palazzo o
        //    creati dall'amministratore.
        $condominio = $condomino->getCondominio();
        if ($condominio === null) {
            // Profilo non associato a un condominio: niente lavori da mostrare.
            $tutte = [];
        } else {
            $tutte = $pm->intervento()->findByCondominio($condominio);
        }

        // 4. CALCOLO I CONTATORI PER LE CARD (sempre sul totale del condominio)
        $contatori = $this->contaPerStato($tutte);

        // 5. LISTA: parto da tutti i lavori del condominio, poi applico
        //    (se presenti) la ricerca per titolo e/o il filtro per stato.
        //    I parametri li legge la View dall'URL.
        $view  = new ViewDashboardCondomino();
        $cerca = $view->getCerca();
        $stato = $view->getStato();
        $categoria = $view->getCategoria();   // id categoria o ''

        if ($cerca !== '' && $condominio !== null) {
            $interventi = $pm->intervento()->cercaByCondominio($condominio, $cerca);
        } else {
            $interventi = $tutte;
        }

        // Filtro per stato (quando l'utente clicca una card contatore).
        if ($stato !== '') {
            $interventi = $this->filtraPerStato($interventi, $stato);
        }

        // Filtro per categoria del fornitore assegnato (menu a tendina).
        if ($categoria !== '') {
            $interventi = $this->filtraPerCategoria($interventi, (int) $categoria);
        }

        // Categorie per popolare il menu a tendina.
        $categorie = $pm->categoria()->findAll();

        // 6. PASSO TUTTO ALLA VIEW
        $view->mostra($condomino, $interventi, $contatori, $cerca, $stato, $categoria, $categorie);
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

    /**
     * Filtra una lista di interventi tenendo solo quelli di un certo stato.
     * Usato quando l'utente clicca una card contatore nella dashboard.
     *
     * @param Intervento[] $interventi
     * @param string       $stato  es. 'completato', 'in_corso', ...
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

    /**
     * Filtra gli interventi per categoria del fornitore assegnato.
     * I lavori senza fornitore (es. solo presentati) vengono esclusi.
     * @param Intervento[] $interventi
     * @return Intervento[]
     */
    private function filtraPerCategoria(array $interventi, int $idCategoria): array
    {
        $out = [];
        foreach ($interventi as $i) {
            $categoria = $i->getStato()?->getFornitore()?->getCategoria();
            if ($categoria !== null && $categoria->getId() === $idCategoria) {
                $out[] = $i;
            }
        }
        return $out;
    }
}
