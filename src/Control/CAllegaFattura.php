<?php
// src/Control/CAllegaFattura.php
//
// OPERAZIONE DI SISTEMA: allegaFattura(idIntervento, filePdf)
// ATTORE: Amministratore.
// PRECONDIZIONE: l'intervento è in stato "Completato".
//
// Caso d'uso (sketch pag. 4): su un lavoro terminato l'admin carica il PDF
// della fattura. Il file viene salvato in uploads/fatture/ e il suo percorso
// memorizzato nello stato Completato (campo 'fattura').
//
// È l'unica operazione del Blocco B che gestisce un upload: legge da $_FILES
// (tramite la View), valida il file e lo sposta con move_uploaded_file().

class CAllegaFattura
{
    // Vincoli sul file accettato.
    private const MAX_BYTES = 5 * 1024 * 1024;          // 5 MB
    private const CARTELLA  = 'uploads/fatture';         // relativa alla root del sito

    public function esegui(): void
    {
        // 1. INPUT (id dalla URL, file dal form — entrambi via la View)
        $view         = new ViewGestioneIntervento();
        $idIntervento = $view->getIdIntervento();
        $file         = $view->getFileFattura();   // l'elemento di $_FILES, o null

        // 2. PERMESSI
        Session::requireRole('amministratore');

        $urlDettaglio = 'index.php?action=dettaglioIntervento&id=' . $idIntervento;

        // 3. VALIDAZIONE — id
        if ($idIntervento <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: index.php?action=dashboardAdmin');
            exit;
        }

        // 3a. VALIDAZIONE — presenza ed esito dell'upload
        if ($file === null || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('errore', 'Carica un file PDF valido.');
            header('Location: ' . $urlDettaglio);
            exit;
        }

        // 3b. VALIDAZIONE — dimensione
        if ($file['size'] <= 0 || $file['size'] > self::MAX_BYTES) {
            Session::setFlash('errore', 'Il file supera la dimensione massima (5 MB).');
            header('Location: ' . $urlDettaglio);
            exit;
        }

        // 3c. VALIDAZIONE — tipo reale (non solo l'estensione del nome).
        //     Controllo il MIME effettivo del contenuto caricato.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if ($mime !== 'application/pdf') {
            Session::setFlash('errore', 'Il file deve essere un PDF.');
            header('Location: ' . $urlDettaglio);
            exit;
        }

        $pm = PersistentManager::getInstance();

        // 4. FOUNDATION — carico l'intervento e controllo lo stato.
        $intervento = $pm->load(Intervento::class, $idIntervento);
        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento inesistente.');
            header('Location: index.php?action=dashboardAdmin');
            exit;
        }
        $stato = $intervento->getStato();
        if (!($stato instanceof Completato)) {
            Session::setFlash('errore', 'La fattura si allega solo a un intervento completato.');
            header('Location: ' . $urlDettaglio);
            exit;
        }

        // 5. SALVATAGGIO FISICO DEL FILE -----------------------------------
        // Cartella di destinazione (assoluta), calcolata dalla root del sito.
        // __DIR__ = .../src/Control -> salgo di due livelli alla root.
        $root          = __DIR__ . '/../..';
        $cartellaAssoluta = $root . '/' . self::CARTELLA;
        if (!is_dir($cartellaAssoluta)) {
            mkdir($cartellaAssoluta, 0777, true);
        }

        // Nome file prevedibile: una sola fattura per intervento (la riallego
        // sovrascrive la precedente). Es: fattura_12.pdf
        $nomeFile      = 'fattura_' . $idIntervento . '.pdf';
        $percorsoRel   = self::CARTELLA . '/' . $nomeFile;     // salvato nel DB
        $percorsoAssol = $cartellaAssoluta . '/' . $nomeFile;  // dove scrivo davvero

        if (!move_uploaded_file($file['tmp_name'], $percorsoAssol)) {
            Session::setFlash('errore', 'Impossibile salvare il file. Riprova.');
            header('Location: ' . $urlDettaglio);
            exit;
        }

        // 6. AGGIORNO LO STATO con il percorso della fattura e salvo.
        $stato->setFattura($percorsoRel);
        $pm->update();

        // 7. ESITO
        Session::setFlash('successo', 'Fattura allegata correttamente.');
        header('Location: ' . $urlDettaglio);
        exit;
    }
}
