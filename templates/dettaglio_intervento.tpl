{* templates/dettaglio_intervento.tpl *}
{* Dettaglio intervento — riferimento UI: sketch_condomino.pdf (caso 8).       *}
{* La View passa: intervento, stato, note[], foto[], errore, successo.         *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="app">

    {* ===== SIDEBAR ===== *}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9v.01"/><path d="M9 12v.01"/><path d="M9 15v.01"/></svg>
            <span>CondoFix</span>
        </div>
        <nav class="sidebar-menu">
            <a href="index.php?action=dashboardCondomino" class="voce-menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
        </nav>
        <a href="index.php?action=logout" class="voce-menu voce-esci">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Esci
        </a>
    </aside>

    {* ===== CONTENUTO ===== *}
    <main class="contenuto">

        <a href="index.php?action=dashboardCondomino" class="link-indietro">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Torna alla dashboard
        </a>

        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}
        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}

        {assign var="tipo" value=$stato->getTipo()}

        <header class="dettaglio-header">
            <h1>{$intervento->getTitolo()|escape}</h1>
            <span class="badge badge-{$tipo|escape}">
                {if $tipo == 'in_corso'}In Corso
                {elseif $tipo == 'presentato'}Presentato
                {elseif $tipo == 'accettato'}Accettato
                {elseif $tipo == 'completato'}Completato
                {elseif $tipo == 'negato'}Negato
                {else}{$tipo|escape}{/if}
            </span>
        </header>

        {* ===== SCHEDA DATI ===== *}
        <div class="scheda">
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
        </div>

        {* ===== DESCRIZIONE ===== *}
        <section class="blocco">
            <h2>Descrizione</h2>
            <p class="testo-descrizione">{$intervento->getDescrizione()|escape|nl2br}</p>
        </section>

        {* ===== STORICO NOTE ===== *}
        <section class="blocco">
            <h2>Storico</h2>
            {if $note|@count == 0}
                <p class="vuoto-inline">Nessuna nota presente.</p>
            {else}
                <ul class="timeline">
                    {foreach $note as $n}
                        <li>
                            <div class="timeline-punto"></div>
                            <div class="timeline-contenuto">
                                <p class="timeline-testo">{$n->getTesto()|escape}</p>
                                <span class="timeline-data">{$n->getTimestamp()->format('d/m/Y H:i')}</span>
                            </div>
                        </li>
                    {/foreach}
                </ul>
            {/if}
        </section>

        {* ===== GALLERIA FOTO ===== *}
        <section class="blocco">
            <h2>Foto lavoro</h2>
            {if $foto|@count == 0}
                <p class="vuoto-inline">Nessuna foto allegata.</p>
            {else}
                <div class="galleria">
                    {foreach $foto as $f}
                        <a href="{$f->getPercorso()|escape}" target="_blank" class="galleria-foto">
                            <img src="{$f->getPercorso()|escape}" alt="{$f->getNomeOriginale()|escape}">
                        </a>
                    {/foreach}
                </div>
            {/if}
        </section>

        {* ===== FATTURA (solo se completato e presente) ===== *}
        {if $tipo == 'completato' && $stato->getFattura()}
        <section class="blocco">
            <h2>Fattura</h2>
            <a href="{$stato->getFattura()|escape}" target="_blank" class="btn-secondario">Apri fattura</a>
        </section>
        {/if}

    </main>

</body>
</html>
