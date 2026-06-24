<?php
// src/Control/CListaLavoratori.php
//
// CONTROLLORE — "visualizza lista Lavoratori" (Amministratore).
// ATTORE: Amministratore.
//
// Mostra l'elenco di tutti i fornitori (lavoratori) registrati nel sistema
// con i loro dati. Stesso schema degli altri Control:
//   1. controllo permessi con Session
//   2. recupero dati SOLO via PersistentManager
//   3. passo i dati alla View

class CListaLavoratori
{
    public function mostra(): void
    {
        Session::requireRole('amministratore');

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) {
            Session::logout();
            return;
        }

        // Solo i fornitori creati da QUESTO amministratore (isolamento dati).
        $lavoratori = $pm->utente()->findFornitoriByAmministratore($admin);

        // Se è stata richiesta la scheda di un lavoratore, preparo il banner.
        $this->preparaBannerInfo($pm, $admin);

        (new ViewListaLavoratori())->mostra($admin, $lavoratori);
    }

    /**
     * Prepara il banner con le specifiche di un lavoratore (senza password),
     * se l'URL lo richiede (?infoLavoratore=ID). Mostra solo i lavoratori
     * creati da questo admin.
     */
    private function preparaBannerInfo(PersistentManager $pm, Amministratore $admin): void
    {
        $idInfo = (int) ($_GET['infoLavoratore'] ?? 0);
        if ($idInfo <= 0) {
            return;
        }
        $l = $pm->load(Fornitore::class, $idInfo);
        // Mostro solo se esiste ed è un lavoratore di QUESTO admin.
        if (!($l instanceof Fornitore) || $l->getAmministratore()?->getId() !== $admin->getId()) {
            return;
        }
        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Scheda lavoratore',
            'senzaIcona'  => true,
            'foto'        => $l->getFotoProfilo(),
            'sottotitolo' => $l->getNome() . ' ' . $l->getCognome(),
            'righe'       => [
                'Nome'        => $l->getNome() . ' ' . $l->getCognome(),
                'Email'       => $l->getEmail(),
                'Telefono'    => $l->getTelefono() ?? '—',
                'Partita IVA' => $l->getPartitaIva() ?? '—',
                'Categoria'   => $l->getCategoria() ? $l->getCategoria()->getNome() : '—',
            ],
        ]);
    }
}
