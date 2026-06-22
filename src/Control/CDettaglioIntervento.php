<?php
// src/Control/CDettaglioIntervento.php
//
// CONTROLLORE — operazione di sistema "Visualizza dettaglio intervento".
//   mostra()  ->  ?action=dettaglioIntervento&id=NN
//
// Mostra un intervento del condomino loggato: dati, stato, note (storico)
// e galleria foto.
//
// SICUREZZA: un condomino può vedere SOLO i propri interventi. Se l'id
// nell'URL non gli appartiene (o non esiste), niente accesso: redirect
// alla dashboard con messaggio. Questo evita che, cambiando l'id a mano
// nell'URL, si possano sbirciare le segnalazioni di altri condomini.

class CDettaglioIntervento
{
    public function mostra(): void
    {
        Session::requireRole('condomino');

        $view = new ViewDettaglioIntervento();

        // 1. INPUT (dalla View)
        $id = $view->getIdIntervento();
        if ($id <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: index.php?action=dashboardCondomino');
            exit;
        }

        $pm = PersistentManager::getInstance();

        // 2. CARICO l'intervento
        $intervento = $pm->load(Intervento::class, $id);
        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento non trovato.');
            header('Location: index.php?action=dashboardCondomino');
            exit;
        }

        // 3. CONTROLLO DI ACCESSO: il condomino può vedere i lavori del PROPRIO
        //    condominio (coerente con la dashboard, che mostra tutti i lavori del
        //    palazzo: quelli segnalati da lui, da altri condomini, o creati
        //    dall'amministratore — questi ultimi non hanno un segnalante).
        $condomino    = $pm->load(Condomino::class, Session::getUserId());
        $mioCondominio = $condomino?->getCondominio();
        $condInterv    = $intervento->getCondominio();

        $stessoCondominio = $mioCondominio !== null
            && $condInterv !== null
            && $condInterv->getId() === $mioCondominio->getId();

        if (!$stessoCondominio) {
            Session::setFlash('errore', 'Non hai accesso a questa segnalazione.');
            header('Location: index.php?action=dashboardCondomino');
            exit;
        }

        // 4. CARICO note e foto tramite la Foundation
        $note = $pm->nota()->findByIntervento($intervento);
        $foto = $pm->foto()->findByIntervento($intervento);

        // 5. PASSO ALLA VIEW
        $view->mostra($intervento, $note, $foto);
    }
}
