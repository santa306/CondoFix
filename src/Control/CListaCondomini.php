<?php
// src/Control/CListaCondomini.php
//
// CONTROLLORE — "visualizza lista Condomini" e "crea nuovo Condominio".
// ATTORE: Amministratore.
//
//   - mostra()     -> lista dei condomini dell'admin   (?action=listaCondomini)
//   - mostraForm() -> form "Nuovo condominio"          (?action=formCreaCondominio)
//   - esegui()     -> POST: crea il condominio          (?action=creaCondominio)
//
// Stesso schema degli altri Control:
//   1. controllo permessi con Session
//   2. dati SOLO via PersistentManager
//   3. passo i dati alla View

class CListaCondomini
{
    // -------------------------------------------------------
    // mostra() — elenco condomini gestiti dall'admin loggato
    // -------------------------------------------------------
    public function mostra(): void
    {
        Session::requireRole('amministratore');

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) {
            Session::logout();
            return;
        }

        $condomini = $pm->condominio()->findByAmministratore($admin);

        (new ViewListaCondomini())->mostra($admin, $condomini);
    }

    // -------------------------------------------------------
    // mostraForm() — form "Nuovo condominio"
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

        (new ViewCreaCondominio())->mostraForm();
    }

    // -------------------------------------------------------
    // esegui() — crea il nuovo condominio
    // -------------------------------------------------------
    public function esegui(): void
    {
        Session::requireRole('amministratore');

        $view = new ViewCreaCondominio();

        // 1. INPUT (dalla View)
        $nome      = $view->getNome();
        $indirizzo = $view->getIndirizzo();
        $citta     = $view->getCitta();

        // 2. VALIDAZIONE
        if ($nome === '' || $indirizzo === '' || $citta === '') {
            Session::setFlash('errore', 'Nome, indirizzo e città sono obbligatori.');
            header('Location: index.php?action=formCreaCondominio');
            exit;
        }

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) {
            Session::logout();
            return;
        }

        // 3. CREO il condominio, assegnato all'admin loggato
        $condominio = new Condominio();
        $condominio->setNome($nome);
        $condominio->setIndirizzo($indirizzo);
        $condominio->setCitta($citta);
        $condominio->setAmministratore($admin);

        // 4. SALVO
        $pm->store($condominio);

        // 5. ESITO
        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Condominio creato',
            'sottotitolo' => 'Il nuovo condominio è stato registrato con successo.',
            'righe'       => [
                'Nome'      => $nome,
                'Indirizzo' => $indirizzo,
                'Città'     => $citta,
            ],
        ]);
        header('Location: index.php?action=listaCondomini');
        exit;
    }
}
