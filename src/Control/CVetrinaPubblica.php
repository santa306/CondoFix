<?php
// src/Control/CVetrinaPubblica.php
//
// CONTROLLORE — "Vetrina pubblica" (Utente non registrato).
//
// Mostra a CHIUNQUE, senza login, alcuni lavori DIMOSTRATIVI per far capire
// come funziona il sistema. NON richiede autenticazione e NON legge dal
// database: i lavori mostrati sono dati di esempio fissi (di "Condominio
// Prova"), definiti qui nel codice. Cosi' nessun dato reale viene mai
// esposto a utenti non autenticati.
//
// Due operazioni:
//   - mostra()          -> elenco dei lavori demo           (?action=vetrina)
//   - mostraDettaglio() -> un singolo lavoro demo per id    (?action=vetrinaDettaglio&id=N)

class CVetrinaPubblica
{
    /**
     * I lavori dimostrativi della vetrina. Dati fissi, non dal DB.
     * Ogni lavoro: id, titolo, descrizione, condominio, stato, data.
     *
     * @return array<int, array<string, string>>
     */
    public static function lavoriDemo(): array
    {
        return [
            1 => [
                'id'          => '1',
                'titolo'      => 'Sostituzione lampadina nell\'androne',
                'descrizione' => 'La lampadina dell\'ingresso principale e\' fulminata e va sostituita. L\'androne resta al buio la sera, creando disagi ai residenti che rientrano.',
                'condominio'  => 'Condominio Prova',
                'stato'       => 'completato',
                'data'        => '12/05/2026',
            ],
            2 => [
                'id'          => '2',
                'titolo'      => 'Perdita d\'acqua nel garage',
                'descrizione' => 'Si e\' formata una piccola pozza d\'acqua vicino al box numero 4. Sembra una perdita da una tubazione del soffitto. Da verificare con un idraulico.',
                'condominio'  => 'Condominio Prova',
                'stato'       => 'in_corso',
                'data'        => '03/06/2026',
            ],
            3 => [
                'id'          => '3',
                'titolo'      => 'Tinteggiatura delle scale',
                'descrizione' => 'Le pareti della tromba delle scale sono ingiallite e segnate. Si richiede un preventivo per la ritinteggiatura completa dal piano terra all\'ultimo piano.',
                'condominio'  => 'Condominio Prova',
                'stato'       => 'accettato',
                'data'        => '15/06/2026',
            ],
            4 => [
                'id'          => '4',
                'titolo'      => 'Citofono non funzionante interno 7',
                'descrizione' => 'Il citofono dell\'interno 7 non squilla quando si chiama dal portone. Probabile guasto alla pulsantiera esterna o al cablaggio.',
                'condominio'  => 'Condominio Prova',
                'stato'       => 'presentato',
                'data'        => '18/06/2026',
            ],
        ];
    }

    // -------------------------------------------------------
    // Elenco dei lavori demo
    // -------------------------------------------------------
    public function mostra(): void
    {
        // NESSUN controllo di permessi: la pagina e' pubblica.
        (new ViewVetrinaPubblica())->mostra(self::lavoriDemo());
    }

    // -------------------------------------------------------
    // Dettaglio di un singolo lavoro demo
    // -------------------------------------------------------
    public function mostraDettaglio(): void
    {
        // Anche questa pagina e' pubblica.
        $view = new ViewVetrinaPubblica();
        $id   = $view->getId();          // legge ?id=N dall'URL

        $lavori = self::lavoriDemo();

        // Se l'id non corrisponde a un lavoro demo, torno alla vetrina.
        if (!isset($lavori[$id])) {
            header('Location: index.php?action=vetrina');
            exit;
        }

        $view->mostraDettaglio($lavori[$id]);
    }
}
