<?php
// src/Control/CDettaglioCondominio.php
//
// CONTROLLORE â€” gestione di un singolo condominio (Amministratore).
//   mostra()           -> ?action=dettaglioCondominio&id=NN
//                         dati condominio + lista condÃ²mini + tasto aggiungi
//   mostraFormCondomino()-> ?action=formCreaCondomino&id=NN  (form nuovo condÃ²mino)
//   creaCondomino()    -> ?action=creaCondomino              (POST: crea il condÃ²mino)
//   mostraInfoCondomino()-> ?action=infoCondomino&id=NN&condominio=MM
//                         banner con tutte le info di un condÃ²mino
//
// SICUREZZA: l'admin gestisce solo i propri condomÃ¬ni.

class CDettaglioCondominio
{
    // -------------------------------------------------------
    // mostra() â€” dettaglio condominio + lista condÃ²mini
    // -------------------------------------------------------
    public function mostra(): void
    {
        Session::requireRole('amministratore');

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) { Session::logout(); return; }

        $id = (int) ($_GET['id'] ?? 0);
        $condominio = $id > 0 ? $pm->load(Condominio::class, $id) : null;

        // Dev'essere un condominio esistente e dell'admin loggato.
        if ($condominio === null || $condominio->getAmministratore()?->getId() !== $admin->getId()) {
            Session::setFlash('errore', 'Condominio non trovato.');
            header('Location: index.php?action=listaCondomini');
            exit;
        }

        $condomini = $pm->utente()->findCondominiByCondominio($condominio);

        // Se Ã¨ stata richiesta la scheda info di un condÃ²mino, la preparo come banner.
        $this->preparaBannerInfo($pm, $condominio);

        (new ViewDettaglioCondominio())->mostra($admin, $condominio, $condomini);
    }

    // -------------------------------------------------------
    // mostraFormCondomino() â€” form per aggiungere un condÃ²mino
    // -------------------------------------------------------
    public function mostraFormCondomino(): void
    {
        Session::requireRole('amministratore');

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) { Session::logout(); return; }

        $id = (int) ($_GET['id'] ?? 0);
        $condominio = $id > 0 ? $pm->load(Condominio::class, $id) : null;
        if ($condominio === null || $condominio->getAmministratore()?->getId() !== $admin->getId()) {
            Session::setFlash('errore', 'Condominio non trovato.');
            header('Location: index.php?action=listaCondomini');
            exit;
        }

        (new ViewCreaCondomino())->mostraForm($condominio);
    }

    // -------------------------------------------------------
    // creaCondomino() â€” crea il condÃ²mino con password temporanea
    // -------------------------------------------------------
    public function creaCondomino(): void
    {
        Session::requireRole('amministratore');

        $view = new ViewCreaCondomino();

        // 1. INPUT
        $idCondominio = (int) ($_POST['idCondominio'] ?? 0);
        $nome      = $view->getNome();
        $cognome   = $view->getCognome();
        $email     = $view->getEmail();
        $interno   = $view->getInterno();
        $password  = $view->getPassword();

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) { Session::logout(); return; }

        $condominio = $idCondominio > 0 ? $pm->load(Condominio::class, $idCondominio) : null;
        if ($condominio === null || $condominio->getAmministratore()?->getId() !== $admin->getId()) {
            Session::setFlash('errore', 'Condominio non valido.');
            header('Location: index.php?action=listaCondomini');
            exit;
        }

        // 2. VALIDAZIONE
        if ($nome === '' || $cognome === '' || $email === '' || $password === '') {
            Session::setFlash('errore', 'Nome, cognome, email e password sono obbligatori.');
            header('Location: index.php?action=formCreaCondomino&id=' . $idCondominio);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('errore', 'Inserisci un indirizzo email valido.');
            header('Location: index.php?action=formCreaCondomino&id=' . $idCondominio);
            exit;
        }
        if (strlen($password) < 8) {
            Session::setFlash('errore', 'La password temporanea deve avere almeno 8 caratteri.');
            header('Location: index.php?action=formCreaCondomino&id=' . $idCondominio);
            exit;
        }
        if ($pm->utente()->findByEmail($email) !== null) {
            Session::setFlash('errore', 'Esiste giÃ  un account con questa email.');
            header('Location: index.php?action=formCreaCondomino&id=' . $idCondominio);
            exit;
        }

        // 3. CREO il condÃ²mino. Flag cambio-password attivo: al primo accesso
        //    sarÃ  costretto a sostituire la password temporanea.
        $condomino = new Condomino();
        $condomino->setNome($nome);
        $condomino->setCognome($cognome);
        $condomino->setEmail($email);
        $condomino->setInterno($interno !== '' ? $interno : null);
        $condomino->setPassword($password);
        $condomino->setCondominio($condominio);
        $condomino->setDeveCambiarePassword(true);

        // 4. SALVO
        $pm->store($condomino);

        // 5. ESITO: banner con le credenziali da consegnare al condÃ²mino.
        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Condomino creato',
            'sottotitolo' => 'Consegna queste credenziali temporanee al condomino.',
            'righe'       => [
                'Nome'      => $nome . ' ' . $cognome,
                'Email'     => $email,
                'Password'  => $password,
                'Interno'   => $interno !== '' ? $interno : '',
                'Condominio'=> $condominio->getNome(),
            ],
        ]);
        header('Location: index.php?action=dettaglioCondominio&id=' . $idCondominio);
        exit;
    }

    

// =======================================================
    // HELPER — prepara il banner con le info di un condomino,
    // se l'URL lo richiede (?infoCondomino=ID).
    // =======================================================
    private function preparaBannerInfo(PersistentManager $pm, Condominio $condominio): void
    {
        $idInfo = (int) ($_GET['infoCondomino'] ?? 0);
        if ($idInfo <= 0) {
            return;
        }
        
        $c = $pm->load(Condomino::class, $idInfo);
        
        // Mostro solo se il condomino esiste e appartiene a QUESTO condominio.
        if ($c === null || $c->getCondominio()?->getId() !== $condominio->getId()) {
            return;
        }

        // 1. Costruisco l'array con le righe fisse (Nome ed Email)
        $righeInfo = [
            'Nome'  => $c->getNome() . ' ' . $c->getCognome(),
            'Email' => $c->getEmail(),
        ];

        // 2. Aggiungo la riga 'Interno' SOLO se non è vuota o null
        $interno = $c->getInterno();
        if ($interno !== null && trim($interno) !== '') {
            $righeInfo['Interno'] = $interno;
        }

        // 3. Aggiungo il condominio
        $righeInfo['Condominio'] = $condominio->getNome();

        // 4. Imposto il banner pulito
        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Scheda Condomino',
            'senzaIcona'  => true,
            'foto'        => $c->getFotoProfilo(),
            'sottotitolo' => $c->getNome() . ' ' . $c->getCognome(),
            'righe'       => $righeInfo,
        ]);
    }
// -------------------------------------------------------
    // eliminaCondomino() — elimina un condòmino del condominio
    // -------------------------------------------------------
    public function eliminaCondomino(): void
    {
        Session::requireRole('amministratore');

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) { Session::logout(); return; }

        $idCondomino = (int) ($_GET['id'] ?? 0);
        $condomino   = $idCondomino > 0 ? $pm->load(Condomino::class, $idCondomino) : null;
        if ($condomino === null) {
            Session::setFlash('errore', 'Condòmino non trovato.');
            header('Location: index.php?action=listaCondomini');
            exit;
        }

        $condominio = $condomino->getCondominio();
        if ($condominio === null || $condominio->getAmministratore()?->getId() !== $admin->getId()) {
            Session::setFlash('errore', 'Non puoi eliminare questo condòmino.');
            header('Location: index.php?action=listaCondomini');
            exit;
        }

        // 1. Scollego il condòmino dagli interventi che ha segnalato (restano nello storico)
        $segnalati = $pm->intervento()->findBySegnalante($condomino);
        if ($segnalati) {
            foreach ($segnalati as $intervento) {
                $intervento->setSegnalante(null);
                $pm->store($intervento);
            }
        }

        // 2. NUOVO BLOCCO: Scollego le note scritte dal condòmino per evitare l'errore Foreign Key
        // Nota: Assicurati che nel tuo PersistentManager la classe per gestire le note
        // si chiami 'nota()' e il metodo di ricerca sia 'findByAutore()'. 
        // Adattalo se usi nomi diversi (es. findNoteByAutore).
        $noteScritte = $pm->nota()->findByAutore($condomino);
        if ($noteScritte) {
            foreach ($noteScritte as $nota) {
                $nota->setAutore(null);
                $pm->store($nota);
            }
        }

        $nomeCondomino = $condomino->getNome() . ' ' . $condomino->getCognome();
        $idRedirect = $condominio->getId();
        
        // 3. Ora che le relazioni con note e interventi sono scollegate, posso eliminare l'utente in sicurezza
        $pm->delete($condomino);

        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Condòmino eliminato',
            'sottotitolo' => 'Il condòmino è stato eliminato. Le sue segnalazioni e note restano nello storico.',
            'righe'       => [ 'Condòmino' => $nomeCondomino ],
        ]);
        header('Location: index.php?action=dettaglioCondominio&id=' . $idRedirect);
        exit;
    }
}

