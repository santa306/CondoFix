<?php
// src/Control/CDettaglioIntervento.php
//
// CONTROLLORE — operazione di sistema "Visualizza dettaglio lavoro" (SSD 6/8 lato fornitore).
//
// Mostra al Fornitore il dettaglio di un singolo intervento a lui assegnato:
// dati, stato corrente, storico delle note operative e galleria foto.
// E' la pagina su cui poi si appoggeranno le verticali aggiungiNota e caricaFoto.
//
// REGOLE ARCHITETTURALI (le stesse del login):
//   - l'input (id) arriva dalla View, non da $_GET diretto
//   - la persistenza passa solo da PersistentManager
//   - i permessi passano solo da Session
//
// SICUREZZA: un fornitore puo' vedere SOLO i lavori assegnati a lui.
//   Senza questo controllo, cambiando l'id nell'URL potrebbe spiare i lavori
//   di altri fornitori. Quindi dopo aver caricato l'intervento verifichiamo
//   che il fornitore dello stato sia quello loggato.

class CDettaglioIntervento
{
    public function mostra(): void
    {
        // 1. PERMESSI
        Session::requireRole('fornitore');

        // 2. INPUT (dalla View)
        $view = new ViewDettaglioIntervento();
        $id   = $view->getIdIntervento();

        // Validazione minima dell'input
        if ($id <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        // 3. FOUNDATION: carico l'intervento
        $pm = PersistentManager::getInstance();
        $intervento = $pm->load(Intervento::class, $id);

        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento non trovato.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        // 4. CONTROLLO DI PROPRIETA'
        //    Lo stato dell'intervento porta con se' il fornitore assegnato.
        //    Confronto il suo id con quello dell'utente loggato.
        $fornitoreAssegnato = $intervento->getStato()?->getFornitore();
        $idLoggato = Session::getUserId();

        if ($fornitoreAssegnato === null
            || $fornitoreAssegnato->getId() !== $idLoggato) {
            // Non e' un lavoro suo: niente accesso.
            Session::setFlash('errore', 'Questo lavoro non e\' assegnato a te.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        // 5. FOUNDATION: note e foto collegate (storico e galleria)
        $note = $pm->nota()->findByIntervento($intervento);
        $foto = $pm->foto()->findByIntervento($intervento);

        // 6. OUTPUT
        $view->mostra($intervento, $note, $foto);
    }
}
