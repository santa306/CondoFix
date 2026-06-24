{* templates/dashboard_admin.tpl *}
{* Dashboard dell'Amministratore — riferimento UI: sketch_amministratore.pdf, pag. 1. *}
{* Layout: sidebar a sinistra + contenuto a destra (saluto, card-contatori, lavori recenti). *}
{* La View passa: titolo, nomeCompleto, contatori (array), recenti (array di Intervento), errore, successo. *}
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

    {* ---------- SIDEBAR ---------- *}
    {include file="_sidebar.tpl"}

    {* ---------- CONTENUTO ---------- *}
    <main class="contenuto">

        {if $successo}
            <div class="avviso avviso-successo">{$successo|escape}</div>
        {/if}
        {if $errore}
            <div class="avviso avviso-errore">{$errore|escape}</div>
        {/if}

        <div class="intestazione">
            <div>
                <h1 class="titolo-pagina">Dashboard</h1>
                <p class="benvenuto">Benvenuto, {$nomeCompleto|escape}</p>
            </div>
            <a href="index.php?action=formCreaIntervento" class="btn-primario">+ Nuovo lavoro</a>
        </div>

        {* ---------- CARD CONTATORI ---------- *}
        <section class="griglia-card">
            <a class="card-contatore" href="index.php?action=dashboardAdmin">
                <div class="numero">{$contatori.totali}</div>
                <div class="etichetta">Lavori totali</div>
            </a>
            <a class="card-contatore" href="index.php?action=dashboardAdmin&stato=presentato">
                <div class="numero">{$contatori.presentati}</div>
                <div class="etichetta">Presentati</div>
            </a>
            <a class="card-contatore" href="index.php?action=dashboardAdmin&stato=accettato">
                <div class="numero">{$contatori.da_fare}</div>
                <div class="etichetta">Accettato</div>
            </a>
            <a class="card-contatore" href="index.php?action=dashboardAdmin&stato=in_corso">
                <div class="numero">{$contatori.in_corso}</div>
                <div class="etichetta">In corso</div>
            </a>
            <a class="card-contatore" href="index.php?action=dashboardAdmin&stato=completato">
                <div class="numero">{$contatori.completati}</div>
                <div class="etichetta">Completati</div>
            </a>
        </section>

        {* --- Filtri: categoria (del fornitore) e condominio --- *}
        <form class="barra-filtri" method="get" action="index.php">
            <input type="hidden" name="action" value="dashboardAdmin">

            <select name="categoria" onchange="this.form.submit()">
                <option value="">Tutte le categorie</option>
                {foreach $categorie as $c}
                    <option value="{$c->getId()}" {if $filtroCategoria == $c->getId()}selected{/if}>{$c->getNome()|escape}</option>
                {/foreach}
            </select>

            <select name="condominio" onchange="this.form.submit()">
                <option value="">Tutti i condomini</option>
                {foreach $condomini as $cond}
                    <option value="{$cond->getId()}" {if $filtroCondominio == $cond->getId()}selected{/if}>{$cond->getNome()|escape}</option>
                {/foreach}
            </select>

            {if $filtroCategoria != '' || $filtroCondominio != ''}
                <a class="ricerca-azzera" href="index.php?action=dashboardAdmin">Azzera filtri</a>
            {/if}
        </form>

        <section class="lavori-recenti">
            <h2>Tutti i lavori</h2>

            {if $recenti}
                {foreach $recenti as $i}
                    <a class="riga-lavoro"
                       href="index.php?action=dettaglioInterventoAdmin&id={$i->getId()}">
                        <div class="riga-titolo">{$i->getTitolo()|escape}</div>
                        <div class="riga-meta">
                            {if $i->getCondominio()}
                                {$i->getCondominio()->getNome()|escape}
                            {/if}
                            {if $i->getStato()->getFornitore() && $i->getStato()->getFornitore()->getCategoria()}
                                &middot; {$i->getStato()->getFornitore()->getCategoria()->getNome()|escape}
                            {/if}
                        </div>
                        <span class="badge badge-{$i->getStato()->getTipo()|escape}">
                            {$i->getStato()->getTipo()|replace:'_':' '|escape}
                        </span>
                    </a>
                {/foreach}
            {else}
                <p class="vuoto">Nessun lavoro presente.</p>
            {/if}
        </section>

        {include file="_banner_esito.tpl"}

    </main>
</div>
</body>
</html>


