<?php
// src/Control/CEliminaLavoratore.php
//
// CONTROLLORE — "elimina Lavoratore (Fornitore)".
// ATTORE: Amministratore.
//
//   - esegui() -> POST: elimina un fornitore (?action=eliminaLavoratore&id=N)
//
// REGOLE DI BUSINESS (decise insieme):
//   1. NON si puo' eliminare un fornitore che ha lavori ATTIVI (Accettato o
//      In Corso): prima vanno completati o riassegnati. Questo evita di
//      perdere lavori ancora in lavorazione.
//   2. I lavori GIA' COMPLETATI restano nel sistema come storico, ma vengono
//      SCOLLEGATI dal fornitore (sullo Stato si imposta fornitore = null).
//   3. Le NOTE scritte dal fornitore restano, ma vengono scollegate
//      (autore = null), perche' l'utente che le ha scritte non esiste piu'.
//
// Questo approccio preserva lo storico del condominio senza lasciare
// riferimenti "rotti" nel database (foreign key verso un utente eliminato).

class CEliminaLavoratore
{
    public function esegui(): void
    {
        // 1. PERMESSI
        Session::requireRole('amministratore');

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) {
            Session::logout();
            return;
        }

        // 2. INPUT: id del fornitore da eliminare
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Session::setFlash('errore', 'Lavoratore non valido.');
            header('Location: index.php?action=listaLavoratori');
            exit;
        }

        // 3. CARICO il fornitore (verificando che sia davvero un Fornitore)
        $fornitore = $pm->load(Fornitore::class, $id);
        if ($fornitore === null) {
            Session::setFlash('errore', 'Lavoratore non trovato.');
            header('Location: index.php?action=listaLavoratori');
            exit;
        }

        // 4. CONTROLLO DI BLOCCO: se ha lavori ATTIVI (Accettato/In Corso)
        //    non si puo' eliminare. L'admin deve prima completarli o riassegnarli.
        $lavoriAttivi = $pm->intervento()->findAttiviByFornitore($fornitore);
        if (count($lavoriAttivi) > 0) {
            Session::setFlash(
                'errore',
                'Impossibile eliminare il lavoratore: ha ' . count($lavoriAttivi) .
                ' lavoro/i ancora attivo/i. Completa o riassegna quei lavori prima di eliminarlo.'
            );
            header('Location: index.php?action=listaLavoratori');
            exit;
        }

        // 5. SCOLLEGO il fornitore da TUTTI i suoi stati (anche orfani).
        //    Una sola query DQL che azzera fornitore_id su tutti gli stati
        //    che referenziano questo fornitore, senza passare dagli interventi.
        $pm->scollegaStatiDaFornitore($fornitore);

        // 6. SCOLLEGO le note scritte dal fornitore (autore = null).
        //    Le note restano nello storico ma senza riferimento all'utente.
        $note = $pm->nota()->findByAutore($fornitore);
        foreach ($note as $nota) {
            $nota->setAutore(null);
            $pm->store($nota);
        }

        // 7. ELIMINO il fornitore
        $nomeFornitore = $fornitore->getNome() . ' ' . $fornitore->getCognome();
        $pm->delete($fornitore);

        // 8. ESITO
        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Lavoratore eliminato',
            'sottotitolo' => 'Il lavoratore e\' stato eliminato. I lavori completati restano nello storico.',
            'righe'       => [
                'Lavoratore' => $nomeFornitore,
            ],
        ]);
        header('Location: index.php?action=listaLavoratori');
        exit;
    }
}
