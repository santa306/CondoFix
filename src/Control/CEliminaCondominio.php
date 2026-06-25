<?php
// src/Control/CEliminaCondominio.php
//
// CONTROLLORE — "elimina Condominio".
// ATTORE: Amministratore.
//
//   - esegui() -> POST: elimina un condominio e tutti i suoi interventi
//                 (?action=eliminaCondominio&id=N)
//
// ATTENZIONE: e' una cancellazione DEFINITIVA. Eliminando il condominio si
// eliminano anche TUTTI i suoi interventi, con i relativi stato, note e foto
// (Doctrine li rimuove in cascata grazie alle relazioni configurate in
// Intervento). Per questo serve un controllo di proprieta' rigoroso: un
// amministratore puo' eliminare SOLO i propri condomini.

class CEliminaCondominio
{
    public function esegui(): void
    {
        // 1. PERMESSI
        Session::requireRole('amministratore');

        $pm    = PersistentManager::getInstance();
        $admin = $pm->load(Amministratore::class, Session::getUserId());
        if ($admin === null) {
            Session::logout();
            return;
        }

        // 2. INPUT: id del condominio da eliminare (dall'URL)
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Session::setFlash('errore', 'Condominio non valido.');
            header('Location: index.php?action=listaCondomini');
            exit;
        }

        // 3. CARICO il condominio
        $condominio = $pm->load(Condominio::class, $id);
        if ($condominio === null) {
            Session::setFlash('errore', 'Condominio non trovato.');
            header('Location: index.php?action=listaCondomini');
            exit;
        }

        // 4. CONTROLLO DI PROPRIETA': l'admin puo' eliminare solo i SUOI condomini.
        //    Confronto l'amministratore del condominio con quello loggato.
        $proprietario = $condominio->getAmministratore();
        if ($proprietario === null || $proprietario->getId() !== $admin->getId()) {
            Session::setFlash('errore', 'Non puoi eliminare un condominio che non gestisci.');
            header('Location: index.php?action=listaCondomini');
            exit;
        }

        // 5. ELIMINO PRIMA TUTTI GLI INTERVENTI del condominio.
        //    Ogni intervento porta con se' (cascade) stato, note e foto.
        //    Va fatto prima di eliminare il condominio, altrimenti la chiave
        //    esterna condominio_id negli interventi causerebbe un errore.
        $interventi = $pm->intervento()->findByCondominio($condominio);
        foreach ($interventi as $intervento) {
            $pm->delete($intervento);
        }

        // 6. ELIMINO I CONDOMINI (utenti residenti) di questo condominio.
        //    Anche loro hanno una FK condominio_id verso il condominio, quindi
        //    vanno rimossi prima del condominio stesso, altrimenti il database
        //    rifiuta la cancellazione (foreign key constraint).
        $residenti = $pm->utente()->findCondominiByCondominio($condominio);
        foreach ($residenti as $residente) {
            $pm->delete($residente);
        }

        // 7. ELIMINO il condominio
        $nomeCondominio = $condominio->getNome();
        $pm->delete($condominio);

        // 8. ESITO
        Session::setBanner([
            'tipo'        => 'successo',
            'titolo'      => 'Condominio eliminato',
            'sottotitolo' => 'Il condominio, i suoi interventi e i suoi condomini sono stati eliminati.',
            'righe'       => [
                'Condominio'         => $nomeCondominio,
                'Interventi rimossi' => (string) count($interventi),
                'Condomini rimossi'  => (string) count($residenti),
            ],
        ]);
        header('Location: index.php?action=listaCondomini');
        exit;
    }
}
