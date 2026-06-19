<?php
// src/Control/CCaricaFoto.php
//
// CONTROLLORE — operazione di sistema "Carica foto" (fornitore).
//
// E' l'unica verticale del blocco che gestisce un UPLOAD DI FILE.
// Il fornitore allega una foto all'intervento dalla pagina di dettaglio.
//
// Flusso:
//   1. permessi + input (id e file da $_FILES, letti dalla View)
//   2. validazione del file: errore upload, dimensione, tipo immagine
//   3. spostamento del file in uploads/foto/ con nome sicuro e univoco
//   4. salvataggio nel DB del solo PERCORSO (non il binario), via entity Foto
//   5. redirect al dettaglio con flash
//
// Nota di sicurezza: non ci si fida mai dell'estensione o del nome originale.
// Si verifica il MIME reale del file e si genera un nome nuovo lato server.

class CCaricaFoto
{
    // Limite dimensione: 5 MB
    private const MAX_BYTES = 5 * 1024 * 1024;

    // Tipi immagine ammessi: mime reale => estensione da usare
    private const TIPI_AMMESSI = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    public function esegui(): void
    {
        // 1. PERMESSI
        Session::requireRole('fornitore');

        // 2. INPUT (dalla View)
        $view = new ViewCaricaFoto();
        $id   = $view->getIdIntervento();
        $file = $view->getFotoCaricata();   // array di $_FILES['foto'] o null

        if ($id <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        // 3. VALIDAZIONE DEL FILE
        //    a) presenza e codice di errore di PHP
        if ($file === null || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('errore', 'Caricamento foto fallito. Riprova.');
            header('Location: index.php?action=dettaglioInterventoFornitore&id=' . $id);
            exit;
        }

        //    b) dimensione
        if ($file['size'] <= 0 || $file['size'] > self::MAX_BYTES) {
            Session::setFlash('errore', 'La foto supera la dimensione massima (5 MB).');
            header('Location: index.php?action=dettaglioInterventoFornitore&id=' . $id);
            exit;
        }

        //    c) tipo reale del file (MIME), non l'estensione dichiarata
        $mime = mime_content_type($file['tmp_name']);
        if (!isset(self::TIPI_AMMESSI[$mime])) {
            Session::setFlash('errore', 'Formato non valido: carica un\'immagine (jpg, png, webp, gif).');
            header('Location: index.php?action=dettaglioInterventoFornitore&id=' . $id);
            exit;
        }
        $estensione = self::TIPI_AMMESSI[$mime];

        // 4. CARICO L'INTERVENTO e controllo che sia assegnato a me
        $pm = PersistentManager::getInstance();
        $intervento = $pm->load(Intervento::class, $id);

        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento non trovato.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        $fornitoreAssegnato = $intervento->getStato()?->getFornitore();
        if ($fornitoreAssegnato === null
            || $fornitoreAssegnato->getId() !== Session::getUserId()) {
            Session::setFlash('errore', 'Questo lavoro non e\' assegnato a te.');
            header('Location: index.php?action=dashboardFornitore');
            exit;
        }

        // 5. SPOSTO IL FILE nella cartella uploads/foto/
        //    La cartella sta nella radice del progetto; __DIR__ e' src/Control,
        //    quindi risalgo di due livelli.
        $cartella = __DIR__ . '/../../uploads/foto';
        if (!is_dir($cartella)) {
            mkdir($cartella, 0777, true);
        }

        // Nome file nuovo, sicuro e univoco (non mi fido del nome originale)
        $nomeFile = 'intervento_' . $id . '_' . uniqid() . '.' . $estensione;
        $percorsoAssoluto = $cartella . '/' . $nomeFile;

        if (!move_uploaded_file($file['tmp_name'], $percorsoAssoluto)) {
            Session::setFlash('errore', 'Impossibile salvare il file sul server.');
            header('Location: index.php?action=dettaglioInterventoFornitore&id=' . $id);
            exit;
        }

        // 6. SALVO NEL DB il percorso relativo (per mostrarlo nel browser)
        $foto = new Foto();
        $foto->setPercorso('uploads/foto/' . $nomeFile);
        $foto->setNomeOriginale(basename($file['name']));  // nome originale, solo per riferimento
        $intervento->addFoto($foto);                        // collega il lato inverso

        $pm->update();   // cascade persist salva la foto

        // 7. ESITO
        Session::setFlash('successo', 'Foto caricata.');
        header('Location: index.php?action=dettaglioInterventoFornitore&id=' . $id);
        exit;
    }
}

