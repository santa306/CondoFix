{* templates/dettaglio_intervento_fornitore.tpl *}
{* Dettaglio lavoro (Fornitore) — layout unificato. *}
{* Variabili: titolo, intervento, tipoStato, note, numeroNote, foto, numeroFoto, errore, successo. *}
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

        <a class="link-indietro" href="index.php?action=dashboardFornitore">&larr; I miei lavori</a>

        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}
        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}

        <div class="dettaglio-testa">
            <h1>{$intervento->getTitolo()|escape}</h1>
            {if $tipoStato == 'accettato'}
                <span class="badge badge-accettato">Da fare</span>
            {elseif $tipoStato == 'in_corso'}
                <span class="badge badge-in_corso">In corso</span>
            {elseif $tipoStato == 'completato'}
                <span class="badge badge-completato">Completato</span>
            {else}
                <span class="badge badge-{$tipoStato|escape}">{$tipoStato|replace:'_':' '|escape}</span>
            {/if}
        </div>

        {* ===== SCHEDA DATI (stesso stile della dashboard admin) ===== *}
        <section class="scheda">
            <div class="riga-dato">
                <span class="etichetta-dato">Condominio</span>
                <span>{$intervento->getCondominio()->getNome()|escape} — {$intervento->getCondominio()->getIndirizzo()|escape}, {$intervento->getCondominio()->getCitta()|escape}</span>
            </div>
            <div class="riga-dato">
                <span class="etichetta-dato">Data creazione</span>
                <span>{$intervento->getDataCreazione()->format('d/m/Y H:i')}</span>
            </div>
            {if $tipoStato == 'completato' && $intervento->getStato()->getDataCompletamento()}
            <div class="riga-dato">
                <span class="etichetta-dato">Data fine lavori</span>
                <span>{$intervento->getStato()->getDataCompletamento()->format('d/m/Y H:i')}</span>
            </div>
            {/if}
            {if $intervento->getStato()->getPriorita()}
            <div class="riga-dato">
                <span class="etichetta-dato">Priorità</span>
                <span>{$intervento->getStato()->getPriorita()|escape}</span>
            </div>
            {/if}
            {if $intervento->getStato()->getFornitore() && $intervento->getStato()->getFornitore()->getCategoria()}
            <div class="riga-dato">
                <span class="etichetta-dato">Categoria</span>
                <span>{$intervento->getStato()->getFornitore()->getCategoria()->getNome()|escape}</span>
            </div>
            {/if}
        </section>

        {* ===== SCHEDA DESCRIZIONE ===== *}
        <section class="scheda">
            <h2>Descrizione</h2>
            <p class="testo-descrizione">{$intervento->getDescrizione()|escape|nl2br}</p>
        </section>

        <div class="dettaglio-azioni">
            {if $tipoStato == 'accettato'}
                <form method="post" action="index.php?action=avviaIntervento" class="form-inline">
                    <input type="hidden" name="id" value="{$intervento->getId()}">
                    <button type="submit" class="btn-primario">Inizia lavoro</button>
                </form>
            {elseif $tipoStato == 'in_corso'}
                <form method="post" action="index.php?action=completaIntervento" class="form-inline">
                    <input type="hidden" name="id" value="{$intervento->getId()}">
                    <button type="submit" class="btn-verde">Completa lavoro</button>
                </form>
            {/if}
        </div>

        <section class="scheda">
            <h2>Storico note ({$numeroNote})</h2>
            {if $numeroNote == 0}
                <p class="vuoto-inline">Nessuna nota operativa per ora.</p>
            {else}
                <ul class="timeline">
                    {foreach $note as $n}
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
            {if $tipoStato == 'accettato' || $tipoStato == 'in_corso'}
                <form method="post" action="index.php?action=aggiungiNota" class="form-nota">
                    <input type="hidden" name="id" value="{$intervento->getId()}">
                    <textarea name="testo" rows="2" placeholder="Aggiungi una nota operativa..." required></textarea>
                    <button type="submit" class="btn-primario">Aggiungi nota</button>
                </form>
            {/if}
        </section>

        <section class="scheda">
            <h2>Foto lavoro ({$numeroFoto})</h2>
            {if $numeroFoto == 0}
                <p class="vuoto-inline">Nessuna foto allegata.</p>
            {else}
                <div class="galleria-foto">
                    {foreach $foto as $f}
                        <a href="{$f->getPercorso()|escape}" target="_blank" class="foto-thumb">
                            <img src="{$f->getPercorso()|escape}" alt="{$f->getNomeOriginale()|escape}">
                        </a>
                    {/foreach}
                </div>
            {/if}
            {if $tipoStato == 'accettato' || $tipoStato == 'in_corso'}
                <form method="post" action="index.php?action=caricaFoto"
                      enctype="multipart/form-data" class="form-foto">
                    <input type="hidden" name="id" value="{$intervento->getId()}">
                    <input type="file" name="foto" accept="image/*" required>
                    <button type="submit" class="btn-primario">Carica foto</button>
                </form>
            {/if}
        </section>

    </main>
</div>
</body>
</html>

