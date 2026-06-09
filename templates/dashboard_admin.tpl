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
<div class="layout-admin">

    {* ---------- SIDEBAR ---------- *}
    <aside class="sidebar">
        <div class="sidebar-logo">CondoFix</div>

        <div class="sidebar-utente">
            <div class="avatar"></div>
            <div>
                <div class="nome">{$nomeCompleto|escape}</div>
                <div class="ruolo">Amministratore</div>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a class="voce attiva" href="index.php?action=dashboardAdmin">Dashboard</a>
            <a class="voce" href="index.php?action=listaCondomini">Condomini</a>
            <a class="voce" href="index.php?action=listaFornitori">Lavoratori</a>
            <a class="voce" href="index.php?action=listaLavori">Lavori</a>
            <a class="voce logout" href="index.php?action=logout">Esci</a>
        </nav>
    </aside>

    {* ---------- CONTENUTO ---------- *}
    <main class="contenuto">

        {if $successo}
            <div class="avviso avviso-successo">{$successo|escape}</div>
        {/if}
        {if $errore}
            <div class="avviso avviso-errore">{$errore|escape}</div>
        {/if}

        <h1 class="titolo-pagina">Dashboard</h1>
        <p class="benvenuto">Benvenuto, {$nomeCompleto|escape}</p>

        {* ---------- CARD CONTATORI ---------- *}
        <section class="griglia-card">
            <div class="card-contatore">
                <div class="numero">{$contatori.totali}</div>
                <div class="etichetta">Lavori totali</div>
            </div>
            <div class="card-contatore">
                <div class="numero">{$contatori.presentati}</div>
                <div class="etichetta">Presentati</div>
            </div>
            <div class="card-contatore">
                <div class="numero">{$contatori.da_fare}</div>
                <div class="etichetta">Da fare</div>
            </div>
            <div class="card-contatore">
                <div class="numero">{$contatori.in_corso}</div>
                <div class="etichetta">In corso</div>
            </div>
            <div class="card-contatore">
                <div class="numero">{$contatori.completati}</div>
                <div class="etichetta">Completati</div>
            </div>
            <div class="card-contatore">
                <div class="numero">{$contatori.condomini}</div>
                <div class="etichetta">Condomini</div>
            </div>
            <div class="card-contatore">
                <div class="numero">{$contatori.lavoratori}</div>
                <div class="etichetta">Lavoratori</div>
            </div>
        </section>

        {* ---------- LAVORI RECENTI ---------- *}
        <section class="lavori-recenti">
            <h2>Lavori recenti</h2>

            {if $recenti}
                {foreach $recenti as $i}
                    <a class="riga-lavoro"
                       href="index.php?action=dettaglioIntervento&id={$i->getId()}">
                        <div class="riga-titolo">{$i->getTitolo()|escape}</div>
                        <div class="riga-meta">
                            {if $i->getCondominio()}
                                {$i->getCondominio()->getNome()|escape}
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

    </main>
</div>
</body>
</html>
