{* templates/dettaglio_intervento_admin.tpl *}
{* Dettaglio intervento (Amministratore) — struttura a schede come condomino/fornitore. *}
{* Mantiene le funzioni admin: nega/approva (se da valutare) e carica fattura (se completato). *}
{* Variabili dalla View: titolo, intervento, fornitori[], daValutare, errore, successo. *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout-app">

    {include file="_sidebar.tpl"}

    <main class="contenuto">

        <a class="link-indietro" href="index.php?action=dashboardAdmin">&larr; Torna alla dashboard</a>

        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}
        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}

        {assign var="tipo" value=$intervento->getStato()->getTipo()}

        <div class="dettaglio-testa">
            <h1>{$intervento->getTitolo()|escape}</h1>
            <span class="badge badge-{$tipo|escape}">{$tipo|replace:'_':' '|escape}</span>
        </div>

        {* ===== SCHEDA DATI ===== *}
        <section class="scheda">
            <div class="riga-dato">
                <span class="etichetta-dato">Condominio</span>
                <span>{if $intervento->getCondominio()}{$intervento->getCondominio()->getNome()|escape} — {$intervento->getCondominio()->getIndirizzo()|escape}, {$intervento->getCondominio()->getCitta()|escape}{else}—{/if}</span>
            </div>
            <div class="riga-dato">
                <span class="etichetta-dato">Data creazione</span>
                <span>{$intervento->getDataCreazione()->format('d/m/Y H:i')}</span>
            </div>
            {if $tipo == 'completato' && $intervento->getStato()->getDataCompletamento()}
            <div class="riga-dato">
                <span class="etichetta-dato">Data fine lavori</span>
                <span>{$intervento->getStato()->getDataCompletamento()->format('d/m/Y H:i')}</span>
            </div>
            {/if}
            {if $intervento->getSegnalante()}
            <div class="riga-dato">
                <span class="etichetta-dato">Segnalato da</span>
                <span>{$intervento->getSegnalante()->getNome()|escape} {$intervento->getSegnalante()->getCognome()|escape} ({$intervento->getSegnalante()->getEmail()|escape})</span>
            </div>
            {/if}
            {if $intervento->getStato()->getPriorita()}
            <div class="riga-dato">
                <span class="etichetta-dato">Priorità</span>
                <span>{$intervento->getStato()->getPriorita()|escape}</span>
            </div>
            {/if}
            {if $intervento->getStato()->getFornitore()}
            <div class="riga-dato">
                <span class="etichetta-dato">Fornitore assegnato</span>
                <span>{$intervento->getStato()->getFornitore()->getNome()|escape} {$intervento->getStato()->getFornitore()->getCognome()|escape}</span>
            </div>
            {/if}
        </section>

        {* ===== AZIONI DI AVANZAMENTO (admin: avvia / completa) ===== *}
        {if $tipo == 'accettato' || $tipo == 'in_corso'}
        <div class="dettaglio-azioni">
            {if $tipo == 'accettato'}
                <form method="post" action="index.php?action=avviaIntervento" class="form-inline">
                    <input type="hidden" name="id" value="{$intervento->getId()}">
                    <button type="submit" class="btn-primario">Inizia lavoro</button>
                </form>
            {elseif $tipo == 'in_corso'}
                <form method="post" action="index.php?action=completaIntervento" class="form-inline">
                    <input type="hidden" name="id" value="{$intervento->getId()}">
                    <button type="submit" class="btn-verde">Completa lavoro</button>
                </form>
            {/if}
        </div>
        {/if}

        {* ===== SCHEDA DESCRIZIONE ===== *}
        <section class="scheda">
            <h2>Descrizione</h2>
            <p class="testo-descrizione">{$intervento->getDescrizione()|escape|nl2br}</p>
        </section>

        {* ===== SCHEDA STORICO NOTE ===== *}
        <section class="scheda">
            <h2>Storico note ({$intervento->getNote()|@count})</h2>
            {if $intervento->getNote()|@count == 0}
                <p class="vuoto-inline">Nessuna nota operativa per ora.</p>
            {else}
                <ul class="timeline">
                    {foreach $intervento->getNote() as $n}
                        <li class="timeline-punto">
                            <p class="timeline-testo">{$n->getTesto()|escape}</p>
                            <span class="timeline-data">{$n->getTimestamp()->format('d/m/Y H:i')}</span>
                            {if $n->getAutore()}
                                <span class="timeline-autore">{$n->getAutore()->getNome()|escape} {$n->getAutore()->getCognome()|escape} ({$n->getAutore()->getRuoloLabel()|escape})</span>
                            {else}
                                <span class="timeline-autore">Autore sconosciuto</span>
                            {/if}
                        </li>
                    {/foreach}
                </ul>
            {/if}
            {if $tipo == 'accettato' || $tipo == 'in_corso'}
                <form method="post" action="index.php?action=aggiungiNota" class="form-nota">
                    <input type="hidden" name="id" value="{$intervento->getId()}">
                    <textarea name="testo" rows="2" placeholder="Aggiungi una nota operativa..." required></textarea>
                    <button type="submit" class="btn-primario">Aggiungi nota</button>
                </form>
            {/if}
        </section>

        {* ===== SCHEDA FOTO ===== *}
        <section class="scheda">
            <h2>Foto lavoro ({$intervento->getFoto()|@count})</h2>
            {if $intervento->getFoto()|@count == 0}
                <p class="vuoto-inline">Nessuna foto allegata.</p>
            {else}
                <div class="galleria-foto">
                    {foreach $intervento->getFoto() as $f}
                        <a href="{$f->getPercorso()|escape}" target="_blank" class="foto-thumb">
                            <img src="{$f->getPercorso()|escape}" alt="{$f->getNomeOriginale()|escape}">
                        </a>
                    {/foreach}
                </div>
            {/if}
            {if $tipo == 'accettato' || $tipo == 'in_corso'}
                <form method="post" action="index.php?action=caricaFoto"
                      enctype="multipart/form-data" class="form-foto">
                    <input type="hidden" name="id" value="{$intervento->getId()}">
                    <input type="file" name="foto" accept="image/*" required>
                    <button type="submit" class="btn-primario">Carica foto</button>
                </form>
            {/if}
        </section>

        {* ===== SCHEDA FATTURA (solo se completato) ===== *}
        {if $tipo == 'completato'}
        <section class="scheda">
            <h2>Fattura</h2>
            {if $intervento->getStato()->getFattura()}
                <p class="riga-dato"><span>Fattura allegata: <a href="{$intervento->getStato()->getFattura()|escape}" target="_blank">apri il PDF</a></span></p>
                <p class="vuoto-inline">Carica un nuovo file per sostituirla.</p>
            {else}
                <p class="vuoto-inline">Fattura mancante.</p>
            {/if}
            <form class="form-azione" method="post" enctype="multipart/form-data"
                  action="index.php?action=allegaFattura&id={$intervento->getId()}">
                <label for="fattura">File PDF della fattura</label>
                <input type="file" id="fattura" name="fattura" accept="application/pdf" required>
                <button type="submit" class="btn-approva">Carica fattura</button>
            </form>
        </section>
        {/if}

        {* ===== SCHEDA VALUTAZIONE (solo se Presentato) ===== *}
        {if $daValutare}
        <section class="scheda">
            <h2>Valuta la segnalazione</h2>
            <div class="due-colonne">

                <form class="form-azione" method="post"
                      action="index.php?action=negaIntervento&id={$intervento->getId()}">
                    <h3>Nega</h3>
                    <label for="motivazione">Motivazione (opzionale)</label>
                    <textarea id="motivazione" name="motivazione" rows="3"
                              placeholder="Perché rifiuti la segnalazione?"></textarea>
                    <button type="submit" class="btn-nega">NEGA</button>
                </form>

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
                                {$forn->getNome()|escape} {$forn->getCognome()|escape}{if $forn->getCategoria()} — {$forn->getCategoria()->getNome()|escape}{/if}
                            </option>
                        {/foreach}
                    </select>
                    <button type="submit" class="btn-approva">APPROVA</button>
                </form>

            </div>
        </section>
        {/if}

    </main>
</div>
</body>
</html>

