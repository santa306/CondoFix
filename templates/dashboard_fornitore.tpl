{* templates/dashboard_fornitore.tpl *}
{* Dashboard del Fornitore — struttura identica all'Amministratore. *}
{* Variabili: titolo, nomeCompleto, contatori{}, numeroLavori, lavori[], errore, successo. *}
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
        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}

        <h1 class="titolo-pagina">I miei lavori</h1>
        <p class="benvenuto">Ciao, {$nomeCompleto|escape}</p>

        <section class="griglia-card">
            <a class="card-contatore" href="index.php?action=dashboardFornitore&stato=tutti"><div class="numero">{$contatori.totali}</div><div class="etichetta">Lavori totali</div></a>
            <a class="card-contatore" href="index.php?action=dashboardFornitore&stato=accettato"><div class="numero">{$contatori.da_fare}</div><div class="etichetta">Da fare</div></a>
            <a class="card-contatore" href="index.php?action=dashboardFornitore&stato=in_corso"><div class="numero">{$contatori.in_corso}</div><div class="etichetta">In corso</div></a>
            <a class="card-contatore" href="index.php?action=dashboardFornitore&stato=completato"><div class="numero">{$contatori.completati}</div><div class="etichetta">Completati</div></a>
        </section>

        {* --- Barra di ricerca per titolo --- *}
        <form class="barra-ricerca" method="get" action="index.php">
            <input type="hidden" name="action" value="dashboardFornitore">
            <input type="text" name="cerca" value="{$cerca|escape}" placeholder="Cerca un lavoro per nome...">
            <button type="submit" class="btn-primario">Cerca</button>
            {if $cerca != ''}<a class="ricerca-azzera" href="index.php?action=dashboardFornitore">Azzera</a>{/if}
        </form>
        {if $cerca != ''}<p class="ricerca-esito">Risultati per: <strong>{$cerca|escape}</strong></p>{/if}

        <section class="lavori-recenti">
            <h2>Lavori attivi</h2>

            {if $numeroLavori == 0}
                <p class="vuoto">Non hai lavori attivi al momento.</p>
            {else}
                {foreach $lavori as $i}
                    {assign var="tipo" value=$i->getStato()->getTipo()}
                    <div class="card-lavoro">
                        <div class="card-lavoro-testa">
                            <h2 class="card-lavoro-titolo">{$i->getTitolo()|escape}</h2>
                            {if $tipo == 'accettato'}
                                <span class="badge badge-accettato">Da fare</span>
                            {elseif $tipo == 'in_corso'}
                                <span class="badge badge-in_corso">In corso</span>
                            {else}
                                <span class="badge badge-{$tipo|escape}">{$tipo|replace:'_':' '|escape}</span>
                            {/if}
                        </div>
                        <p class="card-lavoro-condominio">{$i->getCondominio()->getNome()|escape}</p>
                        <p class="card-lavoro-descrizione">{$i->getDescrizione()|escape}</p>
                        <p class="card-lavoro-data">Creato il {$i->getDataCreazione()->format('d/m/Y')}</p>
                        <div class="card-lavoro-azioni">
                            <a class="btn-secondario" href="index.php?action=dettaglioInterventoFornitore&id={$i->getId()}">Dettaglio</a>
                            {if $tipo == 'accettato'}
                                <form method="post" action="index.php?action=avviaIntervento" class="form-inline">
                                    <input type="hidden" name="id" value="{$i->getId()}">
                                    <button type="submit" class="btn-primario">Inizia lavoro</button>
                                </form>
                            {elseif $tipo == 'in_corso'}
                                <form method="post" action="index.php?action=completaIntervento" class="form-inline">
                                    <input type="hidden" name="id" value="{$i->getId()}">
                                    <button type="submit" class="btn-verde">Completa lavoro</button>
                                </form>
                            {/if}
                        </div>
                    </div>
                {/foreach}
            {/if}
        </section>

    </main>
</div>
</body>
</html>

