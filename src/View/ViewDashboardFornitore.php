<?php
// src/View/ViewDashboardFornitore.php
//
// VIEW della dashboard del Fornitore ("I miei lavori").
//
// OUTPUT (strato Presentation): prepara i dati per dashboard_fornitore.tpl.
// Riceve gia' pronti dal Control: il fornitore, i lavori attivi (per le card
// d'azione) e l'elenco completo dei suoi lavori (per calcolare i contatori,
// nello stesso stile della dashboard Amministratore).
//
// I contatori si calcolano qui contando per tipo di stato: e' presentazione
// di dati, non logica di business (nessuna query, nessuna regola di dominio).

class ViewDashboardFornitore extends ViewBase
{
    /**
     * @param Fornitore $fornitore  utente loggato (per il saluto)
     * @param array     $lavori     interventi attivi (Accettato + InCorso)
     * @param array     $tutti      tutti gli interventi del fornitore (per i contatori)
     */
    // INPUT: termine di ricerca per titolo (da GET, opzionale).
    public function getCerca(): string
    {
        return trim($this->get('cerca'));
    }

    // INPUT: filtro per stato (da GET, opzionale; click su una card contatore).
    public function getStato(): string
    {
        return trim($this->get('stato'));
    }

    public function mostra(Fornitore $fornitore, array $lavori, array $tutti = [], string $cerca = '', string $stato = ''): void
    {
        $this->assign('titolo', 'CondoFix — I miei lavori');
        $this->assign('nomeCompleto', $fornitore->getNome() . ' ' . $fornitore->getCognome());

        // Lista dei lavori attivi e quanti sono ("Hai N lavori attivi")
        $this->assign('lavori',       $lavori);
        $this->assign('numeroLavori', count($lavori));
        $this->assign('cerca',        $cerca);
        $this->assign('stato',        $stato);

        // --- Contatori per stato (come l'Amministratore) ---
        $contatori = [
            'totali'     => count($tutti),
            'da_fare'    => 0,
            'in_corso'   => 0,
            'completati' => 0,
        ];
        foreach ($tutti as $i) {
            switch ($i->getStato()->getTipo()) {
                case 'accettato':  $contatori['da_fare']++;    break;
                case 'in_corso':   $contatori['in_corso']++;   break;
                case 'completato': $contatori['completati']++; break;
            }
        }
        $this->assign('contatori', $contatori);

        $this->assign('errore',   Session::getFlash('errore'));
        $this->assign('successo', Session::getFlash('successo'));

        $this->render('dashboard_fornitore.tpl');
    }
}
