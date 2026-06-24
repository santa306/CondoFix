{* templates/lista_lavoratori.tpl *}
{* Lista di tutti i lavoratori (fornitori) con i loro dati. *}
{* Variabili dalla View: titolo, nomeCompleto, lavoratori[], errore, successo. *}
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

        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}
        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}

        <div class="intestazione">
            <div>
                <h1 class="titolo-pagina">Lavoratori</h1>
                <p class="benvenuto">I lavoratori che hai creato.</p>
            </div>
            <a href="index.php?action=formCreaLavoratore" class="btn-primario">+ Crea lavoratore</a>
        </div>

        <section class="lavori-recenti">
            <h2>I tuoi lavoratori</h2>

            {if $lavoratori}
                {foreach $lavoratori as $l}
                    <a class="riga-lavoro" href="index.php?action=listaLavoratori&infoLavoratore={$l->getId()}">
                        <div class="riga-titolo">{$l->getNome()|escape} {$l->getCognome()|escape}</div>
                        {if $l->getCategoria()}
                            <span class="badge">{$l->getCategoria()->getNome()|escape}</span>
                        {/if}
                    </a>
                {/foreach}
            {else}
                <p class="vuoto">Nessun lavoratore presente.</p>
            {/if}
        </section>

        {include file="_banner_esito.tpl"}

    </main>
</div>
</body>
</html>
