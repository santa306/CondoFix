{* templates/dashboard_fornitore.tpl *}
{* Dashboard del Fornitore — "I miei lavori".                              *}
{* Riferimento UI: sketch_fornitore.pdf (lista card con badge di stato e   *}
{* pulsante "Inizia lavoro" / "Completa lavoro" a seconda dello stato).    *}
{*                                                                          *}
{* Dati passati dalla View:                                                *}
{*   titolo, nomeCompleto, numeroLavori, lavori (array di Intervento),     *}
{*   errore, successo.                                                      *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    {* --- Barra superiore con logo e logout --- *}
    <div class="barra-top">
        <strong class="logo-piccolo">CondoFix</strong>
        <a href="index.php?action=logout">Esci</a>
    </div>

    <main class="contenuto">

        <h1>I miei lavori</h1>
        <p class="saluto">Ciao, {$nomeCompleto|escape}</p>

        {* --- Messaggi flash (esito delle azioni delle altre verticali) --- *}
        {if $errore}
            <div class="avviso avviso-errore">{$errore|escape}</div>
        {/if}
        {if $successo}
            <div class="avviso avviso-successo">{$successo|escape}</div>
        {/if}

        <p class="conteggio">
            {if $numeroLavori == 1}
                Hai 1 lavoro attivo
            {else}
                Hai {$numeroLavori} lavori attivi
            {/if}
        </p>

        {* --- Elenco dei lavori --- *}
        {if $numeroLavori == 0}
            <div class="vuoto">Non hai lavori attivi al momento.</div>
        {else}
            {foreach $lavori as $i}
                {assign var="tipo" value=$i->getStato()->getTipo()}
                <div class="card-lavoro">

                    <div class="card-lavoro-testa">
                        <h2 class="card-lavoro-titolo">{$i->getTitolo()|escape}</h2>

                        {* Badge di stato: classe diversa per colore diverso *}
                        {if $tipo == 'accettato'}
                            <span class="badge badge-dafare">Da fare</span>
                        {elseif $tipo == 'in_corso'}
                            <span class="badge badge-incorso">In corso</span>
                        {else}
                            <span class="badge">{$tipo|escape}</span>
                        {/if}
                    </div>

                    <p class="card-lavoro-condominio">
                        {$i->getCondominio()->getNome()|escape}
                    </p>
                    <p class="card-lavoro-descrizione">
                        {$i->getDescrizione()|escape}
                    </p>
                    <p class="card-lavoro-data">
                        Creato il {$i->getDataCreazione()->format('d/m/Y')}
                    </p>

                    {* --- Azioni: dipendono dallo stato --- *}
                    <div class="card-lavoro-azioni">

                        {* Apri il dettaglio (storico note, foto) *}
                        <a class="btn btn-secondario"
                           href="index.php?action=dettaglioInterventoFornitore&id={$i->getId()}">
                            Dettaglio
                        </a>

                        {if $tipo == 'accettato'}
                            {* Accettato -> InCorso : POST per sicurezza (azione che modifica) *}
                            <form method="post" action="index.php?action=avviaIntervento" class="form-inline">
                                <input type="hidden" name="id" value="{$i->getId()}">
                                <button type="submit" class="btn btn-primario">Inizia lavoro</button>
                            </form>
                        {elseif $tipo == 'in_corso'}
                            {* InCorso -> Completato *}
                            <form method="post" action="index.php?action=completaIntervento" class="form-inline">
                                <input type="hidden" name="id" value="{$i->getId()}">
                                <button type="submit" class="btn btn-verde">Completa lavoro</button>
                            </form>
                        {/if}

                    </div>

                </div>
            {/foreach}
        {/if}

    </main>

</body>
</html>
