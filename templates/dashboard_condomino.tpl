{* templates/dashboard_condomino.tpl *}
{* Dashboard del Condomino — riferimento UI: sketch_condomino.pdf (caso d'uso 7). *}
{* La View passa: titolo, nome, cognome, interventi[], contatori{}, successo.    *}
{*                                                                               *}
{* Regole template (come login.tpl):                                            *}
{*   - sempre |escape sulle variabili che vengono dai dati                       *}
{*   - niente PHP qui dentro                                                     *}
{*   - link e form puntano a index.php?action=...                                *}
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
        <div class="sidebar-logo">CondoFix</div>

        <div class="sidebar-utente">
            <div class="avatar"></div>
            <div>
                <div class="utente-nome">{$nome|escape} {$cognome|escape}</div>
                <div class="utente-ruolo">Condomino</div>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="index.php?action=dashboardCondomino" class="voce-menu attiva">Dashboard</a>
            <a href="index.php?action=dashboardCondomino" class="voce-menu">Lavori</a>
            <a href="index.php?action=logout" class="voce-menu voce-esci">Esci</a>
        </nav>
    </aside>

    {* ===== CONTENUTO ===== *}
    <main class="contenuto">

        {if $successo}
            <div class="avviso avviso-successo">{$successo|escape}</div>
        {/if}

        <div class="intestazione">
            <div>
                <h1>Dashboard</h1>
                <p class="benvenuto">Benvenuto, {$nome|escape} {$cognome|escape}</p>
            </div>
            <a href="index.php?action=formPresentaIntervento" class="btn-primario">
                + Nuova Segnalazione
            </a>
        </div>

        {* ===== CARD CONTATORI ===== *}
        <section class="griglia-contatori">
            <div class="card-contatore">
                <div class="numero">{$contatori.totali}</div>
                <div class="etichetta">Lavori Totali</div>
            </div>
            <div class="card-contatore">
                <div class="numero">{$contatori.presentato}</div>
                <div class="etichetta">Presentati</div>
            </div>
            <div class="card-contatore">
                <div class="numero">{$contatori.accettato}</div>
                <div class="etichetta">Da Fare</div>
            </div>
            <div class="card-contatore">
                <div class="numero">{$contatori.in_corso}</div>
                <div class="etichetta">In Corso</div>
            </div>
            <div class="card-contatore">
                <div class="numero">{$contatori.completato}</div>
                <div class="etichetta">Completati</div>
            </div>
            <div class="card-contatore">
                <div class="numero">{$contatori.negato}</div>
                <div class="etichetta">Negati</div>
            </div>
        </section>

        {* ===== LAVORI RECENTI ===== *}
        <section class="sezione-lista">
            <h2>Lavori recenti</h2>

            {if $interventi|@count == 0}
                <p class="lista-vuota">
                    Non hai ancora inviato segnalazioni.
                    <a href="index.php?action=formPresentaIntervento">Creane una</a>.
                </p>
            {else}
                {foreach $interventi as $i}
                    <a class="card-lavoro"
                       href="index.php?action=dettaglioIntervento&amp;id={$i->getId()}">
                        <div class="card-lavoro-testo">
                            <h3>{$i->getTitolo()|escape}</h3>
                            {if $i->getCondominio()}
                                <p class="meta">{$i->getCondominio()->getNome()|escape}</p>
                            {/if}
                            <p class="descrizione">{$i->getDescrizione()|truncate:90|escape}</p>
                        </div>
                        <div class="card-lavoro-stato">
                            {assign var="tipo" value=$i->getStato()->getTipo()}
                            <span class="badge badge-{$tipo|escape}">
                                {if $tipo == 'in_corso'}In Corso
                                {elseif $tipo == 'presentato'}Presentato
                                {elseif $tipo == 'accettato'}Accettato
                                {elseif $tipo == 'completato'}Completato
                                {elseif $tipo == 'negato'}Negato
                                {else}{$tipo|escape}{/if}
                            </span>
                            <p class="data">{$i->getDataCreazione()->format('d/m/Y')}</p>
                        </div>
                    </a>
                {/foreach}
            {/if}
        </section>

    </main>

</body>
</html>
