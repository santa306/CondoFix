<?php
// src/Control/CCreaIntervento.php
//
// CONTROLLORE — operazione di sistema "Crea intervento" (Amministratore).
//
//   - mostraForm() -> form "Nuovo lavoro"        (?action=formCreaIntervento)
//   - esegui()     -> POST: crea l'Intervento     (?action=creaIntervento)
//
// A differenza della segnalazione del condomino (che nasce Presentata e va
// valutata), il lavoro creato dall'amministratore nasce GIA' ACCETTATO:
// l'admin sceglie condominio, priorita' e fornitore, e il lavoro e' subito
// pronto per essere avviato. Non ha un segnalante (lo crea l'admin):
// il campo segnalante dell'Intervento e' nullable proprio per questo.

class CCreaIntervento
{
    private const PRIORITA_VALIDE = ['alta', 'media', 'bassa'];

    // -------------------------------------------------------
    // mostraForm() — form con le tendine condominio / fornitore / priorita'
    // -------------------------------------------------------
    public function mostraForm(): void
    {
        Session::requireRole('amministratore');

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) {
            Session::logout();
            return;
        }

        // Tendine: i condomini e i fornitori di QUESTO admin (isolamento dati).
        $condomini  = $pm->condominio()->findByAmministratore($admin);
        $fornitori  = $pm->utente()->findFornitoriByAmministratore($admin);

        (new ViewCreaIntervento())->mostraForm($condomini, $fornitori);
    }

    // -------------------------------------------------------
    // esegui() — crea il lavoro gia' Accettato
    // -------------------------------------------------------
    public function esegui(): void
    {
        Session::requireRole('amministratore');

        $view = new ViewCreaIntervento();

        // 1. INPUT (dalla View)
        $titolo        = $view->getTitolo();
        $descrizione   = $view->getDescrizione();
        $idCondominio  = $view->getIdCondominio();
        $idFornitore   = $view->getIdFornitore();
        $priorita      = $view->getPriorita();

        // 2. VALIDAZIONE
        if ($titolo === '' || $descrizione === '') {
            Session::setFlash('errore', 'Titolo e descrizione sono obbligatori.');
            header('Location: index.php?action=formCreaIntervento');
            exit;
        }
        if (!in_array($priorita, self::PRIORITA_VALIDE, true)) {
            Session::setFlash('errore', 'Seleziona una priorità valida.');
            header('Location: index.php?action=formCreaIntervento');
            exit;
        }
        if ($idCondominio <= 0 || $idFornitore <= 0) {
            Session::setFlash('errore', 'Seleziona condominio e fornitore.');
            header('Location: index.php?action=formCreaIntervento');
            exit;
        }

        $pm = PersistentManager::getInstance();

        // Carico l'amministratore loggato (serve per i controlli di proprietà).
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) {
            Session::logout();
            return;
        }

        // 3. CARICO condominio e fornitore scelti e verifico che appartengano
        //    a QUESTO admin (isolamento: non si può assegnare roba altrui).
        $condominio = $pm->load(Condominio::class, $idCondominio);
        if ($condominio === null || $condominio->getAmministratore()?->getId() !== $admin->getId()) {
            Session::setFlash('errore', 'Condominio non valido.');
            header('Location: index.php?action=formCreaIntervento');
            exit;
        }
        $fornitore = $pm->load(Fornitore::class, $idFornitore);
        if (!($fornitore instanceof Fornitore) || $fornitore->getAmministratore()?->getId() !== $admin->getId()) {
            Session::setFlash('errore', 'Fornitore non valido.');
            header('Location: index.php?action=formCreaIntervento');
            exit;
        }

        // 4. CREO L'INTERVENTO gia' in stato ACCETTATO (senza segnalante)
        $intervento = new Intervento();
        $intervento->setTitolo($titolo);
        $intervento->setDescrizione($descrizione);
        $intervento->setCondominio($condominio);
        // niente setSegnalante(): lo crea l'admin, segnalante resta null

        $accettato = new Accettato();
        $accettato->setPriorita($priorita);
        $accettato->setFornitore($fornitore);
        $intervento->setStato($accettato);

        // Nota automatica di avanzamento (timestamp automatico)
        $nota = new Nota();
        $nota->setTesto('Lavoro creato e assegnato dall\'amministratore.');
        $intervento->addNota($nota);

        // 5. SALVO (intervento + stato + nota in cascade)
        $pm->store($intervento);

        // 6. ESITO
        $nomeFornitore = trim($fornitore->getNome() . ' ' . $fornitore->getCognome());
        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Intervento creato',
            'sottotitolo' => 'Il lavoro è stato creato e assegnato al fornitore.',
            'righe'       => [
                'Titolo'          => $titolo,
                'Descrizione'     => $descrizione,
                'Categoria'       => $fornitore->getCategoria() ? $fornitore->getCategoria()->getNome() : '—',
                'Priorità'        => ucfirst($priorita),
                'Condominio'      => $condominio->getNome(),
                'E-mail lavoratore' => $fornitore->getEmail(),
            ],
        ]);
        header('Location: index.php?action=dashboardAdmin');
        exit;
    }
}
