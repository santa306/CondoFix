<?php
// src/Control/CCreaLavoratore.php
//
// CONTROLLORE — creazione di un Lavoratore (Fornitore) da parte dell'admin.
//   mostraForm() -> ?action=formCreaLavoratore   (GET)
//   esegui()     -> ?action=creaLavoratore         (POST)
//
// Il lavoratore nasce collegato all'amministratore che lo crea (isolamento
// dati) e con password temporanea (cambio forzato al primo accesso).

class CCreaLavoratore
{
    // -------------------------------------------------------
    // mostraForm()
    // -------------------------------------------------------
    public function mostraForm(): void
    {
        Session::requireRole('amministratore');

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) { Session::logout(); return; }

        // Categorie esistenti per il menu a tendina (l'admin può anche
        // digitarne una nuova).
        $categorie = $pm->categoria()->findAll();

        (new ViewCreaLavoratore())->mostraForm($categorie);
    }

    // -------------------------------------------------------
    // esegui()
    // -------------------------------------------------------
    public function esegui(): void
    {
        Session::requireRole('amministratore');

        $view = new ViewCreaLavoratore();

        // 1. INPUT
        $nome         = $view->getNome();
        $cognome      = $view->getCognome();
        $email        = $view->getEmail();
        $telefono     = $view->getTelefono();
        $partitaIva   = $view->getPartitaIva();
        $password     = $view->getPassword();
        $idCategoria  = (int) $view->getCategoriaId();
        $nuovaCateg   = $view->getNuovaCategoria();

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) { Session::logout(); return; }

        // 2. VALIDAZIONE
        if ($nome === '' || $cognome === '' || $email === '' || $password === '') {
            Session::setFlash('errore', 'Nome, cognome, email e password sono obbligatori.');
            header('Location: index.php?action=formCreaLavoratore');
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('errore', 'Inserisci un indirizzo email valido.');
            header('Location: index.php?action=formCreaLavoratore');
            exit;
        }
        if (strlen($password) < 8) {
            Session::setFlash('errore', 'La password temporanea deve avere almeno 8 caratteri.');
            header('Location: index.php?action=formCreaLavoratore');
            exit;
        }
        if ($pm->utente()->findByEmail($email) !== null) {
            Session::setFlash('errore', 'Esiste già un account con questa email.');
            header('Location: index.php?action=formCreaLavoratore');
            exit;
        }

        // 3. CATEGORIA: se l'admin ha scritto una categoria nuova la creo,
        //    altrimenti uso quella scelta dal menu (se presente).
        $categoria = null;
        if ($nuovaCateg !== '') {
            $categoria = new Categoria();
            $categoria->setNome($nuovaCateg);
            $pm->store($categoria);
        } elseif ($idCategoria > 0) {
            $categoria = $pm->load(Categoria::class, $idCategoria);
        }

        // 4. CREO il lavoratore, collegato all'admin e con password temporanea.
        $fornitore = new Fornitore();
        $fornitore->setNome($nome);
        $fornitore->setCognome($cognome);
        $fornitore->setEmail($email);
        $fornitore->setTelefono($telefono !== '' ? $telefono : null);
        $fornitore->setPartitaIva($partitaIva !== '' ? $partitaIva : null);
        $fornitore->setPassword($password);
        $fornitore->setCategoria($categoria);
        $fornitore->setAmministratore($admin);
        $fornitore->setDeveCambiarePassword(true);

        // 5. SALVO
        $pm->store($fornitore);

        // 6. ESITO: banner con le credenziali da consegnare al lavoratore.
        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Lavoratore creato',
            'sottotitolo' => 'Consegna queste credenziali temporanee al lavoratore.',
            'righe'       => [
                'Nome'      => $nome . ' ' . $cognome,
                'Email'     => $email,
                'Password'  => $password,
                'Categoria' => $categoria ? $categoria->getNome() : '—',
                'P. IVA'    => $partitaIva !== '' ? $partitaIva : '—',
            ],
        ]);
        header('Location: index.php?action=listaLavoratori');
        exit;
    }
}
