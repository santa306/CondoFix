<?php
// src/Control/CAggiungiNota.php
//
// CONTROLLORE — operazione di sistema "Aggiungi nota operativa".
//
// Aggiunge una nota allo storico dell'intervento. Possono farlo DUE ruoli:
//   - il Fornitore assegnato (solo sui propri lavori)
//   - l'Amministratore (su qualsiasi lavoro, per supervisione)
//
// Non e' una transizione di stato: lavora sulla collection Note via
// $intervento->addNota(). Timestamp automatico. Pattern POST-redirect-GET.

class CAggiungiNota
{
    public function esegui(): void
    {
        // 1. PERMESSI: fornitore OPPURE amministratore
        Session::requireAnyRole(['fornitore', 'amministratore']);
        $isAdmin = (Session::getRuolo() === 'amministratore');

        $tornaDashboard = $isAdmin
            ? 'index.php?action=dashboardAdmin'
            : 'index.php?action=dashboardFornitore';
        $tornaDettaglio = function (int $id) use ($isAdmin) {
            return $isAdmin
                ? 'index.php?action=dettaglioInterventoAdmin&id=' . $id
                : 'index.php?action=dettaglioInterventoFornitore&id=' . $id;
        };

        // 2. INPUT (dalla View)
        $view  = new ViewAggiungiNota();
        $id    = $view->getIdIntervento();
        $testo = $view->getTesto();

        if ($id <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: ' . $tornaDashboard);
            exit;
        }

        // 3. VALIDAZIONE: la nota non puo' essere vuota
        $testo = trim($testo);
        if ($testo === '') {
            Session::setFlash('errore', 'La nota non puo\' essere vuota.');
            header('Location: ' . $tornaDettaglio($id));
            exit;
        }

        // 4. FOUNDATION: carico l'intervento
        $pm = PersistentManager::getInstance();
        $intervento = $pm->load(Intervento::class, $id);

        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento non trovato.');
            header('Location: ' . $tornaDashboard);
            exit;
        }

        // 5. CONTROLLO DI PROPRIETA' — solo per il fornitore
        if (!$isAdmin) {
            $fornitoreAssegnato = $intervento->getStato()?->getFornitore();
            if ($fornitoreAssegnato === null
                || $fornitoreAssegnato->getId() !== Session::getUserId()) {
                Session::setFlash('errore', 'Questo lavoro non e\' assegnato a te.');
                header('Location: ' . $tornaDashboard);
                exit;
            }
        }

        // 6. CREO LA NOTA e la collego all'intervento
        $nota = new Nota();
        $nota->setTesto($testo);
        // Autore della nota: l'utente loggato (Admin o Fornitore).
        $nota->setAutore($pm->load(Utente::class, Session::getUserId()));
        $intervento->addNota($nota);

        $pm->update();

        // 7. ESITO
        Session::setFlash('successo', 'Nota aggiunta.');
        header('Location: ' . $tornaDettaglio($id));
        exit;
    }
}
