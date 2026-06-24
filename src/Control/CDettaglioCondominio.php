<?php
// src/Control/CDettaglioCondominio.php
//
// CONTROLLORE — gestione di un singolo condominio (Amministratore).
//   mostra()           -> ?action=dettaglioCondominio&id=NN
//                         dati condominio + lista condòmini + tasto aggiungi
//   mostraFormCondomino()-> ?action=formCreaCondomino&id=NN  (form nuovo condòmino)
//   creaCondomino()    -> ?action=creaCondomino              (POST: crea il condòmino)
//   mostraInfoCondomino()-> ?action=infoCondomino&id=NN&condominio=MM
//                         banner con tutte le info di un condòmino
//
// SICUREZZA: l'admin gestisce solo i propri condomìni.

class CDettaglioCondominio
{
    // -------------------------------------------------------
    // mostra() — dettaglio condominio + lista condòmini
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

        // Se è stata richiesta la scheda info di un condòmino, la preparo come banner.
        $this->preparaBannerInfo($pm, $condominio);

        (new ViewDettaglioCondominio())->mostra($admin, $condominio, $condomini);
    }

    // -------------------------------------------------------
    // mostraFormCondomino() — form per aggiungere un condòmino
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
    // creaCondomino() — crea il condòmino con password temporanea
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
            Session::setFlash('errore', 'Esiste già un account con questa email.');
            header('Location: index.php?action=formCreaCondomino&id=' . $idCondominio);
            exit;
        }

        // 3. CREO il condòmino. Flag cambio-password attivo: al primo accesso
        //    sarà costretto a sostituire la password temporanea.
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

        // 5. ESITO: banner con le credenziali da consegnare al condòmino.
        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Condòmino creato',
            'sottotitolo' => 'Consegna queste credenziali temporanee al condòmino.',
            'righe'       => [
                'Nome'      => $nome . ' ' . $cognome,
                'Email'     => $email,
                'Password'  => $password,
                'Interno'   => $interno !== '' ? $interno : '—',
                'Condominio'=> $condominio->getNome(),
            ],
        ]);
        header('Location: index.php?action=dettaglioCondominio&id=' . $idCondominio);
        exit;
    }

    // =======================================================
    // HELPER — prepara il banner con le info di un condòmino,
    // se l'URL lo richiede (?infoCondomino=ID).
    // =======================================================
    private function preparaBannerInfo(PersistentManager $pm, Condominio $condominio): void
    {
        $idInfo = (int) ($_GET['infoCondomino'] ?? 0);
        if ($idInfo <= 0) {
            return;
        }
        $c = $pm->load(Condomino::class, $idInfo);
        // Mostro solo se il condòmino esiste e appartiene a QUESTO condominio.
        if ($c === null || $c->getCondominio()?->getId() !== $condominio->getId()) {
            return;
        }
        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Scheda condòmino',
            'senzaIcona'  => true,
            'foto'        => $c->getFotoProfilo(),
            'sottotitolo' => $c->getNome() . ' ' . $c->getCognome(),
            'righe'       => [
                'Nome'       => $c->getNome() . ' ' . $c->getCognome(),
                'Email'      => $c->getEmail(),
                'Interno'    => $c->getInterno() ?? '—',
                'Condominio' => $condominio->getNome(),
            ],
        ]);
    }
}
