<?php
// src/Control/CPresentaIntervento.php
//
// CONTROLLORE — operazioni di sistema legate alla creazione di una segnalazione:
//   - mostraForm()  -> disegna il form "Nuova Segnalazione"   (?action=formPresentaIntervento)
//   - esegui()      -> POST: crea l'Intervento in stato Presentato (?action=presentaIntervento)
//
// Schema identico a CLogin:
//   1. permessi (Session::requireRole)
//   2. input PRESO DALLA VIEW (mai $_POST/$_FILES diretti qui)
//   3. validazione
//   4. Foundation via PersistentManager
//   5. esito + redirect
//
// Scelte concordate:
//   - condominio AUTOMATICO (quello del condomino loggato)
//   - foto caricate SUBITO nel form (upload multiplo, opzionale)
//   - NESSUNA categoria (la assegna l'admin in seguito)

class CPresentaIntervento
{
    // Cartella (relativa alla root del progetto) dove salviamo le foto caricate.
    private const CARTELLA_UPLOAD = 'uploads/foto';

    // Vincoli sugli upload
    private const MAX_FOTO        = 5;
    private const MAX_BYTE        = 5 * 1024 * 1024; // 5 MB per file
    private const ESTENSIONI_OK   = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    // -------------------------------------------------------
    // mostraForm() — disegna il form di nuova segnalazione
    // -------------------------------------------------------
    public function mostraForm(): void
    {
        Session::requireRole('condomino');
        (new ViewPresentaIntervento())->mostraForm();
    }

    // -------------------------------------------------------
    // esegui() — riceve il POST e crea l'intervento
    // -------------------------------------------------------
    public function esegui(): void
    {
        Session::requireRole('condomino');

        $view = new ViewPresentaIntervento();

        // 1. INPUT (dalla View)
        $titolo      = $view->getTitolo();
        $descrizione = $view->getDescrizione();
        $fileFoto    = $view->getFotoCaricate();   // array normalizzato da $_FILES

        // 2. VALIDAZIONE campi testuali
        if ($titolo === '' || $descrizione === '') {
            Session::setFlash('errore', 'Titolo e descrizione sono obbligatori.');
            header('Location: index.php?action=formPresentaIntervento');
            exit;
        }

        $pm = PersistentManager::getInstance();

        // 3. RICAVO IL CONDOMINO LOGGATO e il suo condominio
        $condomino = $pm->load(Condomino::class, Session::getUserId());
        if ($condomino === null) {
            Session::logout();
            return;
        }
        $condominio = $condomino->getCondominio();
        if ($condominio === null) {
            // Il condomino non è associato a nessun condominio: non può segnalare.
            Session::setFlash('errore', 'Il tuo profilo non è associato a un condominio. Contatta l\'amministratore.');
            header('Location: index.php?action=dashboardCondomino');
            exit;
        }

        // 4. CREO L'INTERVENTO in stato Presentato
        $intervento = new Intervento();
        $intervento->setTitolo($titolo);
        $intervento->setDescrizione($descrizione);
        $intervento->setCondominio($condominio);
        $intervento->setSegnalante($condomino);
        $intervento->setStato(new Presentato());   // salvato in cascade

        // 5. GESTIONE FOTO (opzionali). Le salvo su disco e creo le entity Foto.
        //    Lo faccio PRIMA del flush così vengono persistite in cascade insieme
        //    all'intervento. Per il nome file uso un id provvisorio (timestamp);
        //    va benissimo perché il percorso è già univoco.
        $erroreFoto = $this->salvaFoto($fileFoto, $intervento);
        if ($erroreFoto !== null) {
            Session::setFlash('errore', $erroreFoto);
            header('Location: index.php?action=formPresentaIntervento');
            exit;
        }

        // Nota automatica di avanzamento (timestamp automatico), così lo
        // storico parte dalla creazione, come per le azioni dell'admin/fornitore.
        $nota = new Nota();
        $nota->setTesto('Segnalazione inviata.');
        // Autore della nota automatica: chi compie l'azione.
        $nota->setAutore($condomino);
        $intervento->addNota($nota);

        // 6. SALVO TUTTO (intervento + stato + foto in cascade)
        $pm->store($intervento);

        // 7. ESITO
        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Segnalazione inviata',
            'sottotitolo' => 'La tua segnalazione è stata inviata con successo.',
            'righe'       => [
                'Titolo'      => $titolo,
                'Descrizione' => $descrizione,
                'Condominio'  => $condominio->getNome(),
            ],
        ]);
        header('Location: index.php?action=dashboardCondomino');
        exit;
    }

    // =======================================================
    // HELPER PRIVATO — salvataggio fisico dei file + entity Foto
    // Ritorna null se tutto ok, oppure un messaggio d'errore (string).
    // =======================================================
    private function salvaFoto(array $files, Intervento $intervento): ?string
    {
        if (count($files) === 0) {
            return null; // nessuna foto: è consentito
        }
        if (count($files) > self::MAX_FOTO) {
            return 'Puoi caricare al massimo ' . self::MAX_FOTO . ' foto.';
        }

        // Percorso assoluto della cartella di upload (root progetto = due livelli su da src/Control)
        $root          = __DIR__ . '/../..';
        $cartellaAssol = $root . '/' . self::CARTELLA_UPLOAD;

        // Creo la cartella se non esiste
        if (!is_dir($cartellaAssol)) {
            @mkdir($cartellaAssol, 0775, true);
        }

        foreach ($files as $f) {
            // $f = ['name'=>..., 'tmp_name'=>..., 'size'=>..., 'error'=>...]

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

            // Nome file univoco: evita collisioni e caratteri strani
            $nomeFile = 'foto_' . uniqid('', true) . '.' . $est;
            $destAssol = $cartellaAssol . '/' . $nomeFile;

            if (!move_uploaded_file($f['tmp_name'], $destAssol)) {
                return 'Impossibile salvare una foto sul server.';
            }

            // Creo l'entity Foto col percorso RELATIVO (usabile in <img src>)
            $foto = new Foto();
            $foto->setPercorso(self::CARTELLA_UPLOAD . '/' . $nomeFile);
            $foto->setNomeOriginale($f['name']);
            $intervento->addFoto($foto); // setta anche il lato inverso
        }

        return null;
    }
}
