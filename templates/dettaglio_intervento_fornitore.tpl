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

    <aside class="sidebar">
        <div class="sidebar-logo"><img src="img/logo.jpeg" alt="CondoFix"><span>CondoFix</span></div>
        <nav class="sidebar-menu">
            <a class="voce" href="index.php?action=dashboardFornitore">I miei lavori</a>
            <a class="voce logout" href="index.php?action=logout">Esci</a>
        </nav>
    </aside>

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

        <section class="riquadro">
            <p><strong>Condominio:</strong> {$intervento->getCondominio()->getNome()|escape}
               ({$intervento->getCondominio()->getIndirizzo()|escape},
                {$intervento->getCondominio()->getCitta()|escape})</p>
            {if $intervento->getStato()->getPriorita()}
                <p><strong>Priorità:</strong> {$intervento->getStato()->getPriorita()|escape}</p>
            {/if}
            <p><strong>Data creazione:</strong> {$intervento->getDataCreazione()->format('d/m/Y H:i')}</p>
            <p class="descrizione-completa"><strong>Descrizione</strong><br>
               {$intervento->getDescrizione()|escape}</p>
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
            {if $tipoStato == 'accettato' || $tipoStato == 'in_corso'}
                <form method="post" action="index.php?action=aggiungiNota" class="form-nota">
                    <input type="hidden" name="id" value="{$intervento->getId()}">
                    <textarea name="testo" rows="2" placeholder="Aggiungi una nota operativa..." required></textarea>
                    <button type="submit" class="btn-primario">Aggiungi nota</button>
                </form>
            {/if}
        </section>

        <section class="riquadro">
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

