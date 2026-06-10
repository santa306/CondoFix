{* templates/dettaglio_intervento.tpl *}
{* Dettaglio di un singolo intervento (lato Fornitore).                     *}
{* Riferimento UI: sketch_fornitore.pdf (stato, dati, storico note con      *}
{* timestamp, galleria foto).                                               *}
{*                                                                          *}
{* Dati passati dalla View:                                                 *}
{*   titolo, intervento, tipoStato, note, numeroNote, foto, numeroFoto,     *}
{*   errore, successo.                                                      *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    {* --- Barra superiore --- *}
    <div class="barra-top">
        <strong class="logo-piccolo">CondoFix</strong>
        <a href="index.php?action=logout">Esci</a>
    </div>

    <main class="contenuto">

        {* Link di ritorno alla lista *}
        <a class="link-indietro" href="index.php?action=dashboardFornitore">&larr; I miei lavori</a>

        {* --- Messaggi flash --- *}
        {if $errore}
            <div class="avviso avviso-errore">{$errore|escape}</div>
        {/if}
        {if $successo}
            <div class="avviso avviso-successo">{$successo|escape}</div>
        {/if}

        {* --- Intestazione: titolo + badge stato --- *}
        <div class="dettaglio-testa">
            <h1>{$intervento->getTitolo()|escape}</h1>
            {if $tipoStato == 'accettato'}
                <span class="badge badge-dafare">Da fare</span>
            {elseif $tipoStato == 'in_corso'}
                <span class="badge badge-incorso">In corso</span>
            {elseif $tipoStato == 'completato'}
                <span class="badge badge-completato">Completato</span>
            {else}
                <span class="badge">{$tipoStato|escape}</span>
            {/if}
        </div>

        {* --- Dati del lavoro --- *}
        <section class="riquadro">
            <p><strong>Condominio:</strong> {$intervento->getCondominio()->getNome()|escape}
               ({$intervento->getCondominio()->getIndirizzo()|escape},
                {$intervento->getCondominio()->getCitta()|escape})</p>

            {if $intervento->getStato()->getPriorita()}
                <p><strong>Priorità:</strong> {$intervento->getStato()->getPriorita()|escape}</p>
            {/if}

            <p><strong>Data creazione:</strong>
               {$intervento->getDataCreazione()->format('d/m/Y H:i')}</p>

            <p class="descrizione-completa">
                <strong>Descrizione</strong><br>
                {$intervento->getDescrizione()|escape}
            </p>
        </section>

        {* --- Azione coerente con lo stato --- *}
        <div class="dettaglio-azioni">
            {if $tipoStato == 'accettato'}
                <form method="post" action="index.php?action=avviaIntervento" class="form-inline">
                    <input type="hidden" name="id" value="{$intervento->getId()}">
                    <button type="submit" class="btn btn-primario">Inizia lavoro</button>
                </form>
            {elseif $tipoStato == 'in_corso'}
                <form method="post" action="index.php?action=completaIntervento" class="form-inline">
                    <input type="hidden" name="id" value="{$intervento->getId()}">
                    <button type="submit" class="btn btn-verde">Completa lavoro</button>
                </form>
            {/if}
        </div>

        {* --- Storico note operative --- *}
        <section class="riquadro">
            <h2>Storico note ({$numeroNote})</h2>

            {if $numeroNote == 0}
                <p class="vuoto-inline">Nessuna nota operativa per ora.</p>
            {else}
                <ul class="lista-note">
                    {foreach $note as $n}
                        <li class="nota">
                            <span class="nota-data">{$n->getTimestamp()->format('d/m/Y H:i')}</span>
                            <span class="nota-testo">{$n->getTesto()|escape}</span>
                        </li>
                    {/foreach}
                </ul>
            {/if}

            {* PUNTO DI AGGANCIO — verticale 5 (aggiungiNota):
               qui andra' il form per inserire una nuova nota operativa. *}
        </section>

        {* --- Galleria foto --- *}
        <section class="riquadro">
            <h2>Foto lavoro ({$numeroFoto})</h2>

            {if $numeroFoto == 0}
                <p class="vuoto-inline">Nessuna foto allegata.</p>
            {else}
                <div class="galleria-foto">
                    {foreach $foto as $f}
                        <a href="{$f->getPercorso()|escape}" target="_blank" class="foto-thumb">
                            <img src="{$f->getPercorso()|escape}"
                                 alt="{$f->getNomeOriginale()|escape}">
                        </a>
                    {/foreach}
                </div>
            {/if}

            {* PUNTO DI AGGANCIO — verticale 6 (caricaFoto):
               qui andra' il form di upload (enctype multipart/form-data). *}
        </section>

    </main>

</body>
</html>
