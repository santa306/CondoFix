<?php
// src/View/ViewCreaIntervento.php
//
// VIEW del form "Nuovo lavoro" (Amministratore).
//
// RESPONSABILITA' (Presentation):
//   - INPUT: legge titolo, descrizione, condominio scelto, fornitore scelto,
//     priorita' (unico punto che tocca $_POST; il Control non lo vede mai).
//   - OUTPUT: disegna crea_intervento_admin.tpl passando le tendine
//     (condomini gestiti dall'admin e fornitori disponibili).
//
//   Nessuna logica di business, nessun accesso al DB.

class ViewCreaIntervento extends ViewBase
{
    // -------------------------------------------------------
    // INPUT
    // -------------------------------------------------------

    public function getTitolo(): string
    {
        return trim($this->post('titolo'));
    }

    public function getDescrizione(): string
    {
        return trim($this->post('descrizione'));
    }

    public function getIdCondominio(): int
    {
        return (int) $this->post('id_condominio');
    }

    public function getIdFornitore(): int
    {
        return (int) $this->post('id_fornitore');
    }

    public function getPriorita(): string
    {
        return $this->post('priorita');
    }

    // -------------------------------------------------------
    // OUTPUT
    // -------------------------------------------------------

    /**
     * @param array $condomini  condomini gestiti dall'admin (per la tendina)
     * @param array $fornitori  fornitori disponibili (per la tendina)
     */
    public function mostraForm(array $condomini, array $fornitori): void
    {
        $this->assign('titolo',    'CondoFix — Nuovo lavoro');
        $this->assign('condomini', $condomini);
        $this->assign('fornitori', $fornitori);
        $this->assign('errore',    Session::getFlash('errore'));
        $this->assign('successo',  Session::getFlash('successo'));
        $this->render('crea_intervento_admin.tpl');
    }
}
