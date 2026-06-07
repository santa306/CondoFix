<?php
// seed_intervento.php
//
// Script di prova: crea un Intervento di test assegnato al fornitore
// "fornitore@condofix.it" in stato ACCETTATO, cosi' la dashboard del
// fornitore mostra una card con il pulsante "Inizia lavoro" (come nello sketch).
//
// Si appoggia ai dati gia' inseriti da seed.php (fornitore, condominio).
// Usa SOLO le classi gia' esistenti del progetto.

require_once "bootstrap.php";

echo "Creo un intervento di test per il fornitore...\n";

try {
    $pm = PersistentManager::getInstance();

    // 1. Recupero il fornitore di test (gia' creato da seed.php)
    $fornitore = $pm->utente()->findByEmail("fornitore@condofix.it");
    if (!($fornitore instanceof Fornitore)) {
        echo "ERRORE: fornitore@condofix.it non trovato. Lancia prima seed.php.\n";
        exit;
    }

    // 2. Recupero un condominio qualsiasi (il primo disponibile)
    $condomini = $pm->condominio()->findAll();
    if (empty($condomini)) {
        echo "ERRORE: nessun condominio nel DB. Lancia prima seed.php.\n";
        exit;
    }
    $condominio = $condomini[0];

    // 3. Recupero il condomino segnalante (facoltativo)
    $segnalante = $pm->utente()->findByEmail("condomino@condofix.it");

    // 4. Creo lo stato ACCETTATO con priorita' e fornitore assegnato.
    //    (in stato Accettato il lavoro e' pronto per essere avviato dal fornitore)
    $stato = new Accettato();
    $stato->setPriorita('alta');
    $stato->setFornitore($fornitore);

    // 5. Creo l'intervento e gli assegno lo stato
    $intervento = new Intervento();
    $intervento->setTitolo("Perdita bagno");
    $intervento->setDescrizione("Nel mio bagno e' rotto un tubo, esce acqua sotto il lavandino.");
    $intervento->setCondominio($condominio);
    if ($segnalante instanceof Condomino) {
        $intervento->setSegnalante($segnalante);
    }
    $intervento->setStato($stato);

    // 6. Salvo (cascade salva anche lo stato)
    $pm->store($intervento);

    echo "Intervento creato con successo!\n";
    echo "  Titolo: Perdita bagno\n";
    echo "  Stato:  accettato (priorita' alta)\n";
    echo "  Fornitore: Giuseppe Verdi\n\n";
    echo "Ora fai login come fornitore@condofix.it e vedrai la card 'Inizia lavoro'.\n";

} catch (Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}
