<?php
// src/Control/CDashboardFornitore.php
//
// CONTROLLORE — operazione di sistema "Visualizza i lavori assegnati" (SSD 5).
//
// RUOLO NELLO STRATO CONTROL:
//   Coordina il flusso  View -> Control -> Foundation -> View.
//   NON tocca $_POST/$_GET (lo fa la View), NON conosce Doctrine
//   (parla solo con PersistentManager), NON usa Smarty (lo fa la View).
//
// Mostra al Fornitore loggato la lista "I miei lavori" (vedi sketch_fornitore.pdf):
// i lavori ATTIVI assegnati a lui, cioe' quelli in stato Accettato ("Da fare")
// e InCorso, su cui puo' agire con i pulsanti "Inizia lavoro" / "Completa lavoro".

class CDashboardFornitore
{
    // -------------------------------------------------------
    // mostra() — "il sistema mostra la dashboard del fornitore"
    // -------------------------------------------------------
    public function mostra(): void
    {
        // 1. PERMESSI
        //    Solo un fornitore loggato puo' vedere questa pagina.
        //    requireRole chiama gia' requireAuth() al suo interno.
        Session::requireRole('fornitore');

        // 2. RECUPERO L'UTENTE LOGGATO (via Foundation, mai da $_SESSION a mano)
        $pm  = PersistentManager::getInstance();
        $id  = Session::getUserId();
        $fornitore = $pm->load(Fornitore::class, $id);

        // Difesa: se per qualche motivo l'utente non esiste piu' nel DB
        // (es. sessione vecchia), forzo il logout.
        if ($fornitore === null) {
            Session::logout(); // fa redirect a login ed exit
            return;
        }

        // 3. PARLO CON LA FOUNDATION
        //    findAttiviByFornitore() restituisce gli interventi in stato
        //    Accettato + InCorso assegnati a questo fornitore: sono i lavori
        //    su cui puo' agire (lo sketch li chiama "Da fare" / "In corso").
        $lavori = $pm->intervento()->findAttiviByFornitore($fornitore);

        // 4. PASSO TUTTO ALLA VIEW (output)
        $view = new ViewDashboardFornitore();
        $view->mostra($fornitore, $lavori);
    }
}
