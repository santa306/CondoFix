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

        <h1 class="titolo-pagina">Lavoratori</h1>
        <p class="benvenuto">Elenco di tutti i lavoratori registrati.</p>

        <section class="lavori-recenti">
            <h2>Tutti i lavoratori</h2>

            {if $lavoratori}
                {foreach $lavoratori as $l}
                    <div class="riga-lavoro">
                        <div class="riga-titolo">{$l->getNome()|escape} {$l->getCognome()|escape}</div>
                        <div class="riga-meta">
                            {$l->getEmail()|escape}
                            {if $l->getTelefono()} &middot; Tel: {$l->getTelefono()|escape}{/if}
                            {if $l->getPartitaIva()} &middot; P.IVA: {$l->getPartitaIva()|escape}{/if}
                        </div>
                        {if $l->getCategoria()}
                            <span class="badge">{$l->getCategoria()->getNome()|escape}</span>
                        {/if}
                    </div>
                {/foreach}
            {else}
                <p class="vuoto">Nessun lavoratore presente.</p>
            {/if}
        </section>

    </main>
</div>
</body>
</html>
