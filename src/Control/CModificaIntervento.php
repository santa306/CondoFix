<?php
// src/Control/CModificaIntervento.php
//
// CONTROLLORE — modifica di una segnalazione (caso 10 sketch_condomino).
//   mostraForm()  ->  ?action=formModificaIntervento&id=NN
//   esegui()      ->  ?action=modificaIntervento  (POST)
//
// REGOLE concordate:
//   - modificabile SOLO se lo stato è 'presentato'
//   - si modifica la DESCRIZIONE
//   - si possono AGGIUNGERE nuove foto e ELIMINARE quelle esistenti
//   - il titolo NON si tocca
//
// SICUREZZA: l'intervento dev'essere del condomino loggato (come nel dettaglio).

class CModificaIntervento
{
    private const CARTELLA_UPLOAD = 'uploads/foto';
    private const MAX_FOTO        = 5;
    private const MAX_BYTE        = 5 * 1024 * 1024;
    private const ESTENSIONI_OK   = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    // -------------------------------------------------------
    // mostraForm() — disegna il form di modifica precompilato
    // -------------------------------------------------------
    public function mostraForm(): void
    {
        Session::requireRole('condomino');
        $view = new ViewModificaIntervento();

        $intervento = $this->caricaInterventoModificabile($view->getIdIntervento());
        if ($intervento === null) {
            return; // redirect già fatto dentro l'helper
        }

        $foto = PersistentManager::getInstance()->foto()->findByIntervento($intervento);
        $view->mostraForm($intervento, $foto);
    }

    // -------------------------------------------------------
    // esegui() — salva le modifiche
    // -------------------------------------------------------
    public function esegui(): void
    {
        Session::requireRole('condomino');
        $view = new ViewModificaIntervento();

        $id          = $view->getIdIntervento();
        $descrizione = $view->getDescrizione();
        $fotoDaEliminare = $view->getFotoDaEliminare(); // array di id
        $fileNuovi   = $view->getFotoCaricate();

        $intervento = $this->caricaInterventoModificabile($id);
        if ($intervento === null) {
            return;
        }

        // VALIDAZIONE
        if ($descrizione === '') {
            Session::setFlash('errore', 'La descrizione non può essere vuota.');
            header('Location: index.php?action=formModificaIntervento&id=' . $id);
            exit;
        }

        $pm = PersistentManager::getInstance();

        // 1. AGGIORNO LA DESCRIZIONE
        $intervento->setDescrizione($descrizione);

        // 2. ELIMINO LE FOTO RICHIESTE (entity + file fisico)
        if (count($fotoDaEliminare) > 0) {
            $fotoAttuali = $pm->foto()->findByIntervento($intervento);
            foreach ($fotoAttuali as $f) {
                if (in_array($f->getId(), $fotoDaEliminare, true)) {
                    $this->eliminaFileFisico($f->getPercorso());
                    $intervento->removeFoto($f);
                    $pm->delete($f);
                }
            }
        }

        // 3. AGGIUNGO LE NUOVE FOTO (controllo il totale non superi il massimo)
        $rimaste = $pm->foto()->countByIntervento($intervento);
        if (count($fileNuovi) > 0) {
            if ($rimaste + count($fileNuovi) > self::MAX_FOTO) {
                Session::setFlash('errore', 'Puoi avere al massimo ' . self::MAX_FOTO . ' foto in totale.');
                header('Location: index.php?action=formModificaIntervento&id=' . $id);
                exit;
            }
            $errore = $this->salvaFoto($fileNuovi, $intervento);
            if ($errore !== null) {
                Session::setFlash('errore', $errore);
                header('Location: index.php?action=formModificaIntervento&id=' . $id);
                exit;
            }
        }

        // 4. SALVO (le nuove foto vengono persistite in cascade; descrizione e
        //    rimozioni con il flush)
        $pm->store($intervento);

        Session::setFlash('successo', 'Segnalazione aggiornata con successo.');
        header('Location: index.php?action=dettaglioIntervento&id=' . $id);
        exit;
    }

    // =======================================================
    // HELPER PRIVATI
    // =======================================================

    /**
     * Carica l'intervento e verifica: esiste, è del condomino loggato,
     * ed è in stato 'presentato'. In caso contrario fa redirect e ritorna null.
     */
    private function caricaInterventoModificabile(int $id): ?Intervento
    {
        if ($id <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: index.php?action=dashboardCondomino');
            exit;
        }

        $pm = PersistentManager::getInstance();
        $intervento = $pm->load(Intervento::class, $id);

        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento non trovato.');
            header('Location: index.php?action=dashboardCondomino');
            exit;
        }

        $segnalante = $intervento->getSegnalante();
        if ($segnalante === null || $segnalante->getId() !== Session::getUserId()) {
            Session::setFlash('errore', 'Non hai accesso a questa segnalazione.');
            header('Location: index.php?action=dashboardCondomino');
            exit;
        }

        if ($intervento->getStato()?->getTipo() !== 'presentato') {
            Session::setFlash('errore', 'Puoi modificare solo le segnalazioni non ancora gestite.');
            header('Location: index.php?action=dettaglioIntervento&id=' . $id);
            exit;
        }

        return $intervento;
    }

    private function eliminaFileFisico(string $percorsoRelativo): void
    {
        $assoluto = __DIR__ . '/../../' . $percorsoRelativo;
        if (is_file($assoluto)) {
            @unlink($assoluto);
        }
    }

    /** Identico a CPresentaIntervento::salvaFoto. */
    private function salvaFoto(array $files, Intervento $intervento): ?string
    {
        $root          = __DIR__ . '/../..';
        $cartellaAssol = $root . '/' . self::CARTELLA_UPLOAD;
        if (!is_dir($cartellaAssol)) {
            @mkdir($cartellaAssol, 0775, true);
        }

        foreach ($files as $f) {
            if ($f['error'] !== UPLOAD_ERR_OK) {
                return 'Errore nel caricamento di una foto. Riprova.';
            }
            if ($f['size'] > self::MAX_BYTE) {
                return 'Ogni foto deve essere al massimo 5 MB.';
            }
            $est = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            if (!in_array($est, self::ESTENSIONI_OK, true)) {
                return 'Formato non valido. Usa JPG, PNG, WEBP o GIF.';
            }
            $nomeFile  = 'foto_' . uniqid('', true) . '.' . $est;
            $destAssol = $cartellaAssol . '/' . $nomeFile;
            if (!move_uploaded_file($f['tmp_name'], $destAssol)) {
                return 'Impossibile salvare una foto sul server.';
            }
            $foto = new Foto();
            $foto->setPercorso(self::CARTELLA_UPLOAD . '/' . $nomeFile);
            $foto->setNomeOriginale($f['name']);
            $intervento->addFoto($foto);
        }
        return null;
    }
}
