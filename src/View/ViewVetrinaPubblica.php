<?php
// src/View/ViewVetrinaPubblica.php
//
// VIEW della vetrina pubblica (Utente non registrato).
//
// Mostra l'elenco dei lavori in sola lettura. Non legge input (non ci sono
// filtri ne' form), si limita a passare la lista al template pubblico.
// Nessun dato di sessione: la pagina e' accessibile senza login.

class ViewVetrinaPubblica extends ViewBase
{
    /**
     * @param Intervento[] $lavori  tutti i lavori del sistema
     */
    public function mostra(array $lavori): void
    {
        $this->assign('titolo', 'CondoFix — I lavori');
        $this->assign('lavori', $lavori);
        $this->render('vetrina_pubblica.tpl');
    }
}
