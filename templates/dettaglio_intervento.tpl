{* templates/dettaglio_intervento.tpl *}
{* Dettaglio intervento (Condomino) — struttura unificata (sidebar admin). *}
{* Variabili dalla View: titolo, intervento, stato, note[], foto[], errore, successo. *}
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

        <a class="link-indietro" href="index.php?action=dashboardCondomino">&larr; Torna alla dashboard</a>

        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}
        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}

        {assign var="tipo" value=$stato->getTipo()}

        <div class="dettaglio-testa">
            <h1>{$intervento->getTitolo()|escape}</h1>
            <span class="badge badge-{$tipo|escape}">{$tipo|replace:'_':' '|escape}</span>
            {if $tipo == 'presentato'}
                <a href="index.php?action=formModificaIntervento&id={$intervento->getId()}" class="btn-secondario">Modifica</a>
            {/if}
        </div>

        <section class="scheda">
            <div class="riga-dato">
                <span class="etichetta-dato">Condominio</span>
                <span>{if $intervento->getCondominio()}{$intervento->getCondominio()->getNome()|escape}{else}—{/if}</span>
            </div>
            <div class="riga-dato">
                <span class="etichetta-dato">Data creazione</span>
                <span>{$intervento->getDataCreazione()->format('d/m/Y H:i')}</span>
            </div>
            {if $stato->getPriorita()}
            <div class="riga-dato">
                <span class="etichetta-dato">Priorità</span>
                <span>{$stato->getPriorita()|escape}</span>
            </div>
            {/if}
            {if $stato->getFornitore()}
            <div class="riga-dato">
                <span class="etichetta-dato">Fornitore assegnato</span>
                <span>{$stato->getFornitore()->getNome()|escape} {$stato->getFornitore()->getCognome()|escape}</span>
            </div>
            {/if}
            {if $tipo == 'negato' && $stato->getMotivazione()}
            <div class="riga-dato">
                <span class="etichetta-dato">Motivazione rifiuto</span>
                <span>{$stato->getMotivazione()|escape}</span>
            </div>
            {/if}
        </section>

        <section class="scheda">
            <h2>Descrizione</h2>
            <p class="testo-descrizione">{$intervento->getDescrizione()|escape|nl2br}</p>
        </section>

        <section class="scheda">
            <h2>Storico note</h2>
            {if $note|@count == 0}
                <p class="vuoto-inline">Nessuna nota presente.</p>
            {else}
                <ul class="timeline">
                    {foreach $note as $n}
                        <li class="timeline-punto">
                            <p class="timeline-testo">{$n->getTesto()|escape}</p>
                            <span class="timeline-data">{$n->getTimestamp()->format('d/m/Y H:i')}</span>
                        </li>
                    {/foreach}
                </ul>
            {/if}
        </section>

        <section class="scheda">
            <h2>Foto lavoro</h2>
            {if $foto|@count == 0}
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
        </section>

        {if $tipo == 'completato'}
        <section class="scheda">
            <h2>Fattura</h2>
            {if $stato->getFattura()}
                <a href="{$stato->getFattura()|escape}" target="_blank" class="btn-secondario">Apri fattura</a>
            {else}
                <p class="vuoto-inline">Fattura mancante</p>
            {/if}
        </section>
        {/if}

    </main>
</div>
</body>
</html>

