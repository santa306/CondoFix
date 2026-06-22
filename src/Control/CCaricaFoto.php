<?php
// src/Control/CCaricaFoto.php
//
// CONTROLLORE — operazione di sistema "Carica foto".
//
// Unica verticale che gestisce un UPLOAD DI FILE. Allega una foto a un
// intervento. Possono farlo DUE ruoli:
//   - il Fornitore assegnato (solo sui propri lavori)
//   - l'Amministratore (su qualsiasi lavoro, per supervisione)
//
// Sicurezza: non ci si fida dell'estensione o del nome originale; si verifica
// il MIME reale e si genera un nome nuovo lato server. Nel DB si salva solo
// il percorso, non il binario.

class CCaricaFoto
{
    private const MAX_BYTES = 5 * 1024 * 1024;   // 5 MB

    private const TIPI_AMMESSI = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    public function esegui(): void
    {
        // 1. PERMESSI: fornitore OPPURE amministratore
        Session::requireAnyRole(['fornitore', 'amministratore']);
        $isAdmin = (Session::getRuolo() === 'amministratore');

        $tornaDashboard = $isAdmin
            ? 'index.php?action=dashboardAdmin'
            : 'index.php?action=dashboardFornitore';
        $tornaDettaglio = function (int $id) use ($isAdmin) {
            return $isAdmin
                ? 'index.php?action=dettaglioInterventoAdmin&id=' . $id
                : 'index.php?action=dettaglioInterventoFornitore&id=' . $id;
        };

        // 2. INPUT (dalla View)
        $view = new ViewCaricaFoto();
        $id   = $view->getIdIntervento();
        $file = $view->getFotoCaricata();   // array di $_FILES['foto'] o null

        if ($id <= 0) {
            Session::setFlash('errore', 'Intervento non valido.');
            header('Location: ' . $tornaDashboard);
            exit;
        }

        // 3. VALIDAZIONE DEL FILE
        //    a) presenza e codice di errore di PHP
        if ($file === null || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('errore', 'Caricamento foto fallito. Riprova.');
            header('Location: ' . $tornaDettaglio($id));
            exit;
        }

        //    b) dimensione
        if ($file['size'] <= 0 || $file['size'] > self::MAX_BYTES) {
            Session::setFlash('errore', 'La foto supera la dimensione massima (5 MB).');
            header('Location: ' . $tornaDettaglio($id));
            exit;
        }

        //    c) tipo reale del file (MIME), non l'estensione dichiarata
        $mime = mime_content_type($file['tmp_name']);
        if (!isset(self::TIPI_AMMESSI[$mime])) {
            Session::setFlash('errore', 'Formato non valido: carica un\' immagine (jpg, png, webp, gif).');
            header('Location: ' . $tornaDettaglio($id));
            exit;
        }
        $estensione = self::TIPI_AMMESSI[$mime];

        // 4. CARICO L'INTERVENTO
        $pm = PersistentManager::getInstance();
        $intervento = $pm->load(Intervento::class, $id);

        if ($intervento === null) {
            Session::setFlash('errore', 'Intervento non trovato.');
            header('Location: ' . $tornaDashboard);
            exit;
        }

        // 5. CONTROLLO DI PROPRIETA' — solo per il fornitore
        if (!$isAdmin) {
            $fornitoreAssegnato = $intervento->getStato()?->getFornitore();
            if ($fornitoreAssegnato === null
                || $fornitoreAssegnato->getId() !== Session::getUserId()) {
                Session::setFlash('errore', 'Questo lavoro non e\' assegnato a te.');
                header('Location: ' . $tornaDashboard);
                exit;
            }
        }

        // 6. SPOSTO IL FILE nella cartella uploads/foto/
        $cartella = __DIR__ . '/../../uploads/foto';
        if (!is_dir($cartella)) {
            mkdir($cartella, 0777, true);
        }

        $nomeFile = 'intervento_' . $id . '_' . uniqid() . '.' . $estensione;
        $percorsoAssoluto = $cartella . '/' . $nomeFile;

        if (!move_uploaded_file($file['tmp_name'], $percorsoAssoluto)) {
            Session::setFlash('errore', 'Impossibile salvare il file sul server.');
            header('Location: ' . $tornaDettaglio($id));
            exit;
        }

        // 7. SALVO NEL DB il percorso relativo
        $foto = new Foto();
        $foto->setPercorso('uploads/foto/' . $nomeFile);
        $foto->setNomeOriginale(basename($file['name']));
        $intervento->addFoto($foto);

        $pm->update();

        // 8. ESITO
        Session::setFlash('successo', 'Foto caricata.');
        header('Location: ' . $tornaDettaglio($id));
        exit;
    }
}
