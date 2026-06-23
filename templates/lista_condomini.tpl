{* templates/lista_condomini.tpl *}
{* Lista di tutti i condomini + tasto "Nuovo Condominio" in alto a destra. *}
{* Variabili dalla View: titolo, nomeCompleto, condomini[], errore, successo. *}
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
                <h1 class="titolo-pagina">Condomini</h1>
                <p class="benvenuto">Elenco di tutti i condomini gestiti.</p>
            </div>
            <a href="index.php?action=formCreaCondominio" class="btn-primario">+ Nuovo Condominio</a>
        </div>

        <section class="lavori-recenti">
            <h2>Tutti i condomini</h2>

            {if $condomini}
                {foreach $condomini as $c}
                    <div class="riga-lavoro">
                        <div class="riga-titolo">{$c->getNome()|escape}</div>
                        <div class="riga-meta">
                            {$c->getIndirizzo()|escape} &middot; {$c->getCitta()|escape}
                        </div>
                    </div>
                {/foreach}
            {else}
                <p class="vuoto">Nessun condominio presente.</p>
            {/if}
        </section>

        {include file="_banner_esito.tpl"}

    </main>
</div>
</body>
</html>
