<?php
// src/View/ViewModificaIntervento.php
//
// VIEW del form di modifica segnalazione.
//   - INPUT: id, descrizione, foto da eliminare (id[]), nuove foto ($_FILES).
//   - OUTPUT: form precompilato con descrizione attuale + foto esistenti.

class ViewModificaIntervento extends ViewBase
{
    // -------------------------------------------------------
    // INPUT
    // -------------------------------------------------------
    public function getIdIntervento(): int
    {
        return (int) $this->get('id');
    }

    public function getDescrizione(): string
    {
        return $this->post('descrizione');
    }

    /**
     * Lista di id (interi) delle foto che l'utente ha spuntato per l'eliminazione.
     * Arrivano dai checkbox name="elimina_foto[]".
     */
    public function getFotoDaEliminare(): array
    {
        $raw = $_POST['elimina_foto'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        return array_map('intval', $raw);
    }

    /**
     * Nuove foto caricate. Normalizza $_FILES['foto'] come in ViewPresentaIntervento.
     */
    public function getFotoCaricate(): array
    {
        if (!isset($_FILES['foto']) || !is_array($_FILES['foto']['name'])) {
            return [];
        }
        $out = [];
        $names = $_FILES['foto']['name'];
        for ($i = 0; $i < count($names); $i++) {
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
    /**
     * @param Intervento $intervento
     * @param Foto[]     $foto  foto attualmente allegate
     */
    public function mostraForm(Intervento $intervento, array $foto): void
    {
        $this->assign('titolo',     'CondoFix — Modifica Segnalazione');
        $this->assign('intervento', $intervento);
        $this->assign('foto',       $foto);
        $this->assign('errore',     Session::getFlash('errore'));
        $this->assign('successo',   Session::getFlash('successo'));
        $this->render('modifica_intervento.tpl');
    }
}
