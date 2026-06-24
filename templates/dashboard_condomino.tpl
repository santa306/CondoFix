{* templates/dashboard_condomino.tpl *}
{* Dashboard del Condomino — struttura identica all'Amministratore. *}
{* La View passa: titolo, nome, cognome, interventi[], contatori{}, successo. *}
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

        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}

        <div class="intestazione">
            <div>
                <h1>Dashboard</h1>
                <p class="benvenuto">Benvenuto, {$nome|escape} {$cognome|escape}</p>
            </div>
            <a href="index.php?action=formPresentaIntervento" class="btn-primario">+ Nuova Segnalazione</a>
        </div>

        <section class="griglia-card">
            <a class="card-contatore" href="index.php?action=dashboardCondomino"><div class="numero">{$contatori.totali}</div><div class="etichetta">Lavori totali</div></a>
            <a class="card-contatore" href="index.php?action=dashboardCondomino&stato=presentato"><div class="numero">{$contatori.presentato}</div><div class="etichetta">Presentati</div></a>
            <a class="card-contatore" href="index.php?action=dashboardCondomino&stato=accettato"><div class="numero">{$contatori.accettato}</div><div class="etichetta">Accettato</div></a>
            <a class="card-contatore" href="index.php?action=dashboardCondomino&stato=in_corso"><div class="numero">{$contatori.in_corso}</div><div class="etichetta">In corso</div></a>
            <a class="card-contatore" href="index.php?action=dashboardCondomino&stato=completato"><div class="numero">{$contatori.completato}</div><div class="etichetta">Completati</div></a>
            <a class="card-contatore" href="index.php?action=dashboardCondomino&stato=negato"><div class="numero">{$contatori.negato}</div><div class="etichetta">Negati</div></a>
        </section>

        {* --- Filtro per categoria (del fornitore assegnato) --- *}
        <form class="barra-filtri" method="get" action="index.php">
            <input type="hidden" name="action" value="dashboardCondomino">
            <select name="categoria" onchange="this.form.submit()">
                <option value="">Tutte le categorie</option>
                {foreach $categorie as $cat}
                    <option value="{$cat->getId()}" {if $filtroCategoria == $cat->getId()}selected{/if}>{$cat->getNome()|escape}</option>
                {/foreach}
            </select>
            {if $filtroCategoria != ''}
                <a class="ricerca-azzera" href="index.php?action=dashboardCondomino">Azzera filtro</a>
            {/if}
        </form>

        <section class="lavori-recenti">
            <h2>Lavori recenti</h2>
            {if $interventi|@count == 0}
                <p class="vuoto">Non hai ancora inviato segnalazioni. <a href="index.php?action=formPresentaIntervento">Creane una</a>.</p>
            {else}
                {foreach $interventi as $i}
                    <a class="riga-lavoro" href="index.php?action=dettaglioIntervento&id={$i->getId()}">
                        <div class="riga-titolo">{$i->getTitolo()|escape}</div>
                        <div class="riga-meta">{if $i->getCondominio()}{$i->getCondominio()->getNome()|escape}{/if}{if $i->getStato()->getFornitore() && $i->getStato()->getFornitore()->getCategoria()} &middot; {$i->getStato()->getFornitore()->getCategoria()->getNome()|escape}{/if}</div>
                        {assign var="tipo" value=$i->getStato()->getTipo()}
                        <span class="badge badge-{$tipo|escape}">{$tipo|replace:'_':' '|escape}</span>
                    </a>
                {/foreach}
            {/if}
        </section>

        {include file="_banner_esito.tpl"}

    </main>
</div>
</body>
</html>

