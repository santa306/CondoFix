{* templates/dettaglio_condominio.tpl *}
{* Dettaglio di un condominio: dati, tasto aggiungi condòmino, lista condòmini. *}
{* Variabili: titolo, nomeCompleto, condominio, condomini[], banner, flash. *}
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

        <a class="link-indietro" href="index.php?action=listaCondomini">&larr; Torna ai condomini</a>

        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}
        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}

        <div class="intestazione">
            <div>
                <h1 class="titolo-pagina">{$condominio->getNome()|escape}</h1>
                <p class="benvenuto">{$condominio->getIndirizzo()|escape} &middot; {$condominio->getCitta()|escape}</p>
            </div>
            <a href="index.php?action=formCreaCondomino&id={$condominio->getId()}" class="btn-primario">+ Aggiungi condòmino</a>
        </div>

        <section class="lavori-recenti">
            <h2>Condòmini ({$condomini|@count})</h2>

            {if $condomini}
                {foreach $condomini as $c}
                    <div class="riga-lavoro riga-con-azioni">
                        <a class="riga-link"
                           href="index.php?action=dettaglioCondominio&id={$condominio->getId()}&infoCondomino={$c->getId()}">
                            <div class="riga-titolo">{$c->getNome()|escape} {$c->getCognome()|escape}</div>
                            <div class="riga-meta">
                                {$c->getEmail()|escape}
                                {if $c->getInterno()} &middot; {$c->getInterno()|escape}{/if}
                            </div>
                        </a>
                        <a class="btn-elimina"
                           href="index.php?action=eliminaCondomino&id={$c->getId()}"
                           onclick="return confirm('Eliminare il condòmino {$c->getNome()|escape} {$c->getCognome()|escape}? Le sue segnalazioni resteranno nello storico ma senza il suo nome.');">
                           Elimina
                        </a>
                    </div>
                {/foreach}
            {else}
                <p class="vuoto">Nessun condòmino in questo condominio. Aggiungi il primo.</p>
            {/if}
        </section>

        {include file="_banner_esito.tpl"}

    </main>
</div>
</body>
</html>
