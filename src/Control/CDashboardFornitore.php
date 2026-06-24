<?php
// src/Control/CDashboardFornitore.php
//
// CONTROLLORE — operazione di sistema "Visualizza i lavori assegnati" (SSD 5).
//
// Mostra al Fornitore loggato la dashboard "I miei lavori":
//   - card contatori (come l'Amministratore): totali / da fare / in corso / completati
//   - lista dei lavori ATTIVI (Accettato + InCorso) su cui puo' agire
//
// Coordina il flusso View -> Control -> Foundation -> View. Non tocca
// $_POST/$_GET, non conosce Doctrine, non usa Smarty.

class CDashboardFornitore
{
    public function mostra(): void
    {
        // 1. PERMESSI
        Session::requireRole('fornitore');

        // 2. UTENTE LOGGATO (via Foundation)
        $pm  = PersistentManager::getInstance();
        $id  = Session::getUserId();
        $fornitore = $pm->load(Fornitore::class, $id);

        if ($fornitore === null) {
            Session::logout();
            return;
        }

        // 3. FOUNDATION
        //    - lavori ATTIVI (Accettato + InCorso): sono le card su cui agire
        //    - TUTTI i lavori del fornitore: servono per i contatori
        //    I contatori si calcolano sempre su TUTTI i lavori; la lista
        //    attiva viene filtrata se c'e' una ricerca per titolo.
        $tutti = $pm->intervento()->findByFornitore($fornitore);

        // 4. LISTA: ricerca per titolo e/o filtro per stato (click su card).
        //    La ricerca lavora sugli attivi; il filtro per stato parte da
        //    TUTTI i lavori del fornitore (cosi' puo' mostrare anche i
        //    completati, che non sono tra gli "attivi").
        $view  = new ViewDashboardFornitore();
        $cerca = $view->getCerca();
        $stato = $view->getStato();
        $condominioFiltro = $view->getCondominio();   // id condominio o ''

        if ($cerca !== '') {
            // ricerca per titolo (su tutti i suoi lavori)
            $lavori = $this->filtraPerTitolo($tutti, $cerca);
        } elseif ($stato === 'tutti') {
            // click su "Lavori totali": mostro TUTTI i suoi lavori
            $lavori = $tutti;
        } elseif ($stato !== '') {
            // click su una card di stato: filtro tutti i suoi lavori per stato
            $lavori = $this->filtraPerStato($tutti, $stato);
        } else {
            // vista di default: TUTTI i lavori del fornitore (così appena entra
            // vede subito ogni lavoro, compresi i completati).
            $lavori = $tutti;
        }

        // Filtro per condominio (menu a tendina).
        if ($condominioFiltro !== '') {
            $lavori = $this->filtraPerCondominio($lavori, (int) $condominioFiltro);
        }

        // Lista dei condomìni in cui il fornitore ha lavori (per il menu).
        $condominiFornitore = $this->condominiDi($tutti);

        // 5. OUTPUT
        $view->mostra($fornitore, $lavori, $tutti, $cerca, $stato, $condominioFiltro, $condominiFornitore);
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

    /**
     * Filtra per titolo (ricerca testuale, case-insensitive) su una lista.
     * @param Intervento[] $interventi
     * @return Intervento[]
     */
    private function filtraPerTitolo(array $interventi, string $cerca): array
    {
        $ago = mb_strtolower($cerca);
        $out = [];
        foreach ($interventi as $i) {
            if (mb_strpos(mb_strtolower($i->getTitolo()), $ago) !== false) {
                $out[] = $i;
            }
        }
        return $out;
    }

    /**
     * Filtra gli interventi per condominio.
     * @param Intervento[] $interventi
     * @return Intervento[]
     */
    private function filtraPerCondominio(array $interventi, int $idCondominio): array
    {
        $out = [];
        foreach ($interventi as $i) {
            if ($i->getCondominio() !== null && $i->getCondominio()->getId() === $idCondominio) {
                $out[] = $i;
            }
        }
        return $out;
    }

    /**
     * Estrae la lista (senza duplicati) dei condomìni presenti in un insieme
     * di interventi: serve a popolare il menu a tendina del filtro.
     * @param Intervento[] $interventi
     * @return Condominio[]
     */
    private function condominiDi(array $interventi): array
    {
        $map = [];
        foreach ($interventi as $i) {
            $c = $i->getCondominio();
            if ($c !== null) {
                $map[$c->getId()] = $c;   // la chiave-id elimina i duplicati
            }
        }
        return array_values($map);
    }
}
