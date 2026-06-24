<?php
// src/Control/CDashboardAdmin.php
//
// CONTROLLORE — operazione di sistema "visualizza Dashboard Amministratore".
// ATTORE: Amministratore.
//
// Caso d'uso (sketch pag. 1, "Monitoraggio"):
//   Dopo il login l'admin vede la sua dashboard con:
//     - i contatori in tempo reale (card): totali, presentati, da fare,
//       in corso, completati, condomini gestiti, lavoratori
//     - l'elenco dei lavori recenti
//
// RUOLO NELLO STRATO CONTROL (identico allo schema di CLogin):
//   1. controllo permessi con Session
//   2. recupero i dati SOLO via PersistentManager (mai Doctrine qui)
//   3. passo i dati alla View, che li disegna col template Smarty
//
// NB: lascio intatto il vecchio CDashboard (lo usano le dashboard di
//     fornitore e condomino degli altri blocchi). Questa è la versione
//     "vera" della sola dashboard admin, collegata al case 'dashboardAdmin'.

class CDashboardAdmin
{
    public function mostra(): void
    {
        // 1. PERMESSI — solo l'amministratore loggato può vedere questa pagina.
        Session::requireRole('amministratore');

        // 2. DATI (solo tramite PersistentManager) -------------------------
        $pm = PersistentManager::getInstance();

        // 2a. L'amministratore loggato (mi serve per "i suoi condomini").
        //     L'id è in sessione; carico l'oggetto Entity corrispondente.
        $admin = $pm->load(Amministratore::class, Session::getUserId());

        // 2a. Condomìni gestiti da QUESTO admin: base dell'isolamento dati.
        $mieiCondomini = $pm->condominio()->findByAmministratore($admin);

        // 2b. Interventi dei SOLI condomìni dell'admin, raggruppati per stato.
        $perStato = $pm->intervento()->findGroupedByStatoForCondomini($mieiCondomini);

        // 2c. Contatori delle card.
        //     "Da fare" = lavori accettati ma non ancora avviati.
        $contatori = [
            'totali'      => array_sum(array_map('count', $perStato)),
            'presentati'  => count($perStato['presentato']),
            'da_fare'     => count($perStato['accettato']),
            'in_corso'    => count($perStato['in_corso']),
            'completati'  => count($perStato['completato']),
            // Condomini e lavoratori dell'admin loggato.
            'condomini'   => count($mieiCondomini),
            'lavoratori'  => count($pm->utente()->findFornitoriByAmministratore($admin)),
        ];

        // 2d. Lista lavori con filtri per CATEGORIA (del fornitore assegnato)
        //     e per CONDOMINIO. I valori dei filtri li legge la View.
        $view       = new ViewDashboardAdmin();
        $stato      = $view->getStato();
        $categoria  = $view->getCategoria();   // id categoria (string) o ''
        $condominio = $view->getCondominio();  // id condominio (string) o ''

        // Parto dai lavori dei SOLI condomìni dell'admin (dal più recente).
        $recenti = $pm->intervento()->findByCondomini($mieiCondomini);

        // Filtro per stato (click su una card contatore).
        if ($stato !== '') {
            $recenti = $this->filtraPerStato($recenti, $stato);
        }
        // Filtro per condominio (menu a tendina).
        if ($condominio !== '') {
            $recenti = $this->filtraPerCondominio($recenti, (int) $condominio);
        }
        // Filtro per categoria del fornitore assegnato (menu a tendina).
        if ($categoria !== '') {
            $recenti = $this->filtraPerCategoria($recenti, (int) $categoria);
        }

        // Liste per popolare i due menu a tendina.
        $categorie    = $pm->categoria()->findAll();
        $condominiAdm = $mieiCondomini;

        // 3. PASSO TUTTO ALLA VIEW -----------------------------------------
        $view->mostra($admin, $contatori, $recenti, $stato, $categoria, $condominio, $categorie, $condominiAdm);
    }

    /**
     * Filtra gli interventi per condominio.
     * @param Intervento[] $interventi
     * @return Intervento[]
     */
    private function filtraPerCondominio(array $interventi, int $idCondominio): array
    {
        $out = [];
        foreach ($interventi as $i) {
            if ($i->getCondominio() !== null && $i->getCondominio()->getId() === $idCondominio) {
                $out[] = $i;
            }
        }
        return $out;
    }

    /**
     * Filtra gli interventi per categoria del FORNITORE assegnato.
     * I lavori senza fornitore (es. solo presentati) vengono esclusi.
     * @param Intervento[] $interventi
     * @return Intervento[]
     */
    private function filtraPerCategoria(array $interventi, int $idCategoria): array
    {
        $out = [];
        foreach ($interventi as $i) {
            $fornitore = $i->getStato()?->getFornitore();
            $categoria = $fornitore?->getCategoria();
            if ($categoria !== null && $categoria->getId() === $idCategoria) {
                $out[] = $i;
            }
        }
        return $out;
    }

    /**
     * Filtra una lista di interventi per stato (click su una card contatore).
     * @param Intervento[] $interventi
     * @return Intervento[]
     */
    private function filtraPerStato(array $interventi, string $stato): array
    {
        $out = [];
        foreach ($interventi as $i) {
            if ($i->getStato()?->getTipo() === $stato) {
                $out[] = $i;
            }
        }
        return $out;
    }
}
