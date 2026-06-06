<?php
// src/View/ViewPresentaIntervento.php
//
// VIEW del form "Nuova Segnalazione".
//
// RESPONSABILITA' (Presentation):
//   - INPUT: legge titolo, descrizione e file caricati (unico punto che
//     tocca $_POST e $_FILES; il Control non li vede mai direttamente).
//   - OUTPUT: disegna il template presenta_intervento.tpl.
//
//   Nessuna logica di business, nessun accesso al DB.

class ViewPresentaIntervento extends ViewBase
{
    // -------------------------------------------------------
    // INPUT
    // -------------------------------------------------------

    public function getTitolo(): string
    {
        return $this->post('titolo');
    }

    public function getDescrizione(): string
    {
        return $this->post('descrizione');
    }

    /**
     * Normalizza l'upload multiplo di $_FILES['foto'] in una lista semplice
     * di file, scartando gli slot vuoti (quando l'utente non carica nulla).
     *
     * Restituisce un array di voci:
     *   ['name'=>..., 'tmp_name'=>..., 'size'=>..., 'error'=>...]
     *
     * Questo è l'UNICO punto che conosce la struttura di $_FILES: il Control
     * riceve una lista pulita e non deve sapere com'è fatto l'array PHP.
     */
    public function getFotoCaricate(): array
    {
        if (!isset($_FILES['foto']) || !is_array($_FILES['foto']['name'])) {
            return [];
        }

        $out   = [];
        $names = $_FILES['foto']['name'];

        for ($i = 0; $i < count($names); $i++) {
            // Slot vuoto (nessun file scelto in quella posizione): salto
            if ($_FILES['foto']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $out[] = [
                'name'     => $_FILES['foto']['name'][$i],
                'tmp_name' => $_FILES['foto']['tmp_name'][$i],
                'size'     => $_FILES['foto']['size'][$i],
                'error'    => $_FILES['foto']['error'][$i],
            ];
        }

        return $out;
    }

    // -------------------------------------------------------
    // OUTPUT
    // -------------------------------------------------------

    public function mostraForm(): void
    {
        $this->assign('titolo',   'CondoFix — Nuova Segnalazione');
        $this->assign('errore',   Session::getFlash('errore'));
        $this->assign('successo', Session::getFlash('successo'));
        $this->render('presenta_intervento.tpl');
    }
}
