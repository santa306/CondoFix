{* templates/dettaglio_intervento.tpl *}
{* Dettaglio di un singolo intervento — riferimento UI: sketch_amministratore.pdf pag. 3. *}
{* Variabili dalla View: titolo, intervento (Intervento), fornitori (array), *}
{* daValutare (bool), errore, successo. *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout-admin">

    {* ---------- SIDEBAR ---------- *}
    <aside class="sidebar">
        <div class="sidebar-logo">CondoFix</div>
        <nav class="sidebar-menu">
            <a class="voce" href="index.php?action=dashboardAdmin">Dashboard</a>
            <a class="voce" href="index.php?action=listaCondomini">Condomini</a>
            <a class="voce" href="index.php?action=listaFornitori">Lavoratori</a>
            <a class="voce" href="index.php?action=listaLavori">Lavori</a>
            <a class="voce logout" href="index.php?action=logout">Esci</a>
        </nav>
    </aside>

    {* ---------- CONTENUTO ---------- *}
    <main class="contenuto">

        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}
        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}

        <a class="link-indietro" href="index.php?action=dashboardAdmin">&larr; Torna alla dashboard</a>

        <h1 class="titolo-pagina">{$intervento->getTitolo()|escape}</h1>

        <p class="riga-stato">
            STATO:
            <span class="badge badge-{$intervento->getStato()->getTipo()|escape}">
                {$intervento->getStato()->getTipo()|replace:'_':' '|escape}
            </span>
        </p>

        {* ---------- DATI INTERVENTO ---------- *}
        <section class="scheda">
            <div class="campo">
                <span class="etichetta-campo">Condominio</span>
                {if $intervento->getCondominio()}
                    {$intervento->getCondominio()->getNome()|escape} —
                    {$intervento->getCondominio()->getIndirizzo()|escape},
                    {$intervento->getCondominio()->getCitta()|escape}
                {else}—{/if}
            </div>

            <div class="campo">
                <span class="etichetta-campo">Data creazione</span>
                {$intervento->getDataCreazione()->format('d/m/Y H:i')}
            </div>

            {if $intervento->getSegnalante()}
                <div class="campo">
                    <span class="etichetta-campo">Segnalato da</span>
                    {$intervento->getSegnalante()->getNome()|escape}
                    {$intervento->getSegnalante()->getCognome()|escape}
                    ({$intervento->getSegnalante()->getEmail()|escape})
                </div>
            {/if}

            {* Priorità e fornitore esistono solo dagli stati accettato in poi *}
            {if $intervento->getStato()->getPriorita()}
                <div class="campo">
                    <span class="etichetta-campo">Priorità</span>
                    {$intervento->getStato()->getPriorita()|escape}
                </div>
            {/if}
            {if $intervento->getStato()->getFornitore()}
                <div class="campo">
                    <span class="etichetta-campo">Fornitore assegnato</span>
                    {$intervento->getStato()->getFornitore()->getNome()|escape}
                    {$intervento->getStato()->getFornitore()->getCognome()|escape}
                </div>
            {/if}

            <div class="campo">
                <span class="etichetta-campo">Descrizione</span>
                <p class="descrizione">{$intervento->getDescrizione()|escape}</p>
            </div>
        </section>

        {* ---------- FOTO ---------- *}
        <section class="scheda">
            <h2>Foto lavoro</h2>
            {if $intervento->getFoto()|@count > 0}
                <div class="galleria-foto">
                    {foreach $intervento->getFoto() as $f}
                        <div class="foto-box">
                            <img src="{$f->getPercorso()|escape}"
                                 alt="{$f->getNomeOriginale()|escape}">
                        </div>
                    {/foreach}
                </div>
            {else}
                <p class="vuoto">Nessuna foto allegata.</p>
            {/if}
        </section>

        {* ---------- FATTURA (solo se l'intervento è Completato) ---------- *}
        {if $intervento->getStato()->getTipo() == 'completato'}
        <section class="scheda">
            <h2>Fattura</h2>

            {if $intervento->getStato()->getFattura()}
                <p class="campo">
                    Fattura allegata:
                    <a href="{$intervento->getStato()->getFattura()|escape}" target="_blank">
                        apri il PDF
                    </a>
                </p>
                <p class="etichetta-campo">Carica un nuovo file per sostituirla.</p>
            {else}
                <p class="vuoto">Fattura mancante.</p>
            {/if}

            <form class="form-azione" method="post"
                  enctype="multipart/form-data"
                  action="index.php?action=allegaFattura&id={$intervento->getId()}">
                <label for="fattura">File PDF della fattura</label>
                <input type="file" id="fattura" name="fattura" accept="application/pdf" required>
                <button type="submit" class="btn btn-approva">Carica fattura</button>
            </form>
        </section>
        {/if}

        {* ---------- AZIONI (solo se l'intervento è ancora "Presentato") ---------- *}
        {if $daValutare}
        <section class="scheda azioni">
            <h2>Valuta la segnalazione</h2>

            <div class="due-colonne">

                {* --- FORM NEGA --- *}
                <form class="form-azione" method="post"
                      action="index.php?action=negaIntervento&id={$intervento->getId()}">
                    <h3>Nega</h3>
                    <label for="motivazione">Motivazione (opzionale)</label>
                    <textarea id="motivazione" name="motivazione" rows="3"
                              placeholder="Perché rifiuti la segnalazione?"></textarea>
                    <button type="submit" class="btn btn-nega">NEGA</button>
                </form>

                {* --- FORM APPROVA --- *}
                <form class="form-azione" method="post"
                      action="index.php?action=accettaIntervento&id={$intervento->getId()}">
                    <h3>Approva</h3>

                    <label for="priorita">Priorità</label>
                    <select id="priorita" name="priorita" required>
                        <option value="">Seleziona…</option>
                        <option value="alta">Alta</option>
                        <option value="media">Media</option>
                        <option value="bassa">Bassa</option>
                    </select>

                    <label for="id_fornitore">Fornitore</label>
                    <select id="id_fornitore" name="id_fornitore" required>
                        <option value="">Seleziona…</option>
                        {foreach $fornitori as $forn}
                            <option value="{$forn->getId()}">
                                {$forn->getNome()|escape} {$forn->getCognome()|escape}
                                {if $forn->getCategoria()} — {$forn->getCategoria()->getNome()|escape}{/if}
                            </option>
                        {/foreach}
                    </select>

                    <button type="submit" class="btn btn-approva">APPROVA</button>
                </form>

            </div>
        </section>
        {/if}

    </main>
</div>
</body>
</html>
