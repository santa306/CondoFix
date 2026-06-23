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

        if ($cerca !== '') {
            // ricerca per titolo (tra i lavori attivi)
            $lavori = $pm->intervento()->cercaAttiviByFornitore($fornitore, $cerca);
        } elseif ($stato === 'tutti') {
            // click su "Lavori totali": mostro TUTTI i suoi lavori
            $lavori = $tutti;
        } elseif ($stato !== '') {
            // click su una card di stato: filtro tutti i suoi lavori per stato
            $lavori = $this->filtraPerStato($tutti, $stato);
        } else {
            // vista di default: solo i lavori attivi (accettato + in corso)
            $lavori = $pm->intervento()->findAttiviByFornitore($fornitore);
        }

        // 5. OUTPUT
        $view->mostra($fornitore, $lavori, $tutti, $cerca, $stato);
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
