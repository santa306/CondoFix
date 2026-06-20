<?php
// src/Control/CAggiungiNota.php
//
// CONTROLLORE — operazione di sistema "Aggiungi nota operativa" (fornitore).
//
// Il fornitore, dalla pagina di dettaglio, scrive una nota operativa
// (es. "smontato il sifone") che viene aggiunta allo storico dell'intervento.
//
// Non e' una transizione di stato: lavora sulla collection Note dell'intervento
// tramite $intervento->addNota(...). Il timestamp della nota e' automatico
// (impostato nel costruttore di Nota). Pattern POST-redirect-GET: dopo il
// salvataggio si torna al dettaglio con un flash.

class CAggiungiNota
{
    public function esegui(): void
    {
        // 1. PERMESSI
        Session::requireRole('fornitore');

        // 2. INPUT (dalla View)
        $view  = new ViewAggiungiNota();
        $id    = $view->getIdIntervento();
        $testo = $view->getTesto();

        if ($id <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        // 3. VALIDAZIONE: la nota non puo' essere vuota
        $testo = trim($testo);
        if ($testo === '') {
            Session::setFlash('errore', 'La nota non puo\' essere vuota.');
            header('Location: index.php?action=dettaglioInterventoFornitore&id=' . $id);
            exit;
        }

        // 4. FOUNDATION: carico l'intervento
        $pm = PersistentManager::getInstance();
        $intervento = $pm->load(Intervento::class, $id);

        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento non trovato.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        // 5. CONTROLLO DI PROPRIETA': il lavoro deve essere assegnato a me
        $fornitoreAssegnato = $intervento->getStato()?->getFornitore();
        if ($fornitoreAssegnato === null
            || $fornitoreAssegnato->getId() !== Session::getUserId()) {
            Session::setFlash('errore', 'Questo lavoro non e\' assegnato a te.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        // 6. CREO LA NOTA e la collego all'intervento
        //    addNota() imposta gia' il lato inverso ($nota->setIntervento()).
        //    Il timestamp e' automatico (costruttore di Nota).
        $nota = new Nota();
        $nota->setTesto($testo);
        $intervento->addNota($nota);

        // Il cascade persist sulla collection note salva la nota con l'update.
        $pm->update();

        // 7. ESITO
        Session::setFlash('successo', 'Nota aggiunta.');
        header('Location: index.php?action=dettaglioInterventoFornitore&id=' . $id);
        exit;
    }
}

