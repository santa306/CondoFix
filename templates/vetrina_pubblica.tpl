{* templates/vetrina_pubblica.tpl *}
{* Vetrina pubblica (Utente non registrato): elenco lavori in sola lettura. *}
{* Mostra titolo, descrizione, condominio, stato e data. Niente dati personali *}
{* operativi (segnalante, fornitore, note, foto). Variabili: titolo, lavori[]. *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="pagina-pubblica">

    <header class="testa-pubblica">
        <div class="logo-pubblico">
            <img src="img/logo.jpeg" alt="CondoFix">
            <span>CondoFix</span>
        </div>
        <a href="index.php?action=login" class="btn-accedi-piccolo">Accedi</a>
    </header>

    <main class="contenuto-pubblico">
        <h1 class="titolo-pagina">I lavori di CondoFix</h1>
        <p class="benvenuto">Stai esplorando il sistema come visitatore. Per gestire i lavori effettua l'accesso.</p>

        {if $lavori|@count == 0}
            <p class="vuoto">Nessun lavoro da mostrare.</p>
        {else}
            <section class="lista-pubblica">
                {foreach $lavori as $l}
                    {assign var="tipo" value=$l->getStato()->getTipo()}
                    <article class="card-lavoro-pubblica">
                        <div class="riga-titolo">
                            <h2>{$l->getTitolo()|escape}</h2>
                            <span class="badge badge-{$tipo|escape}">{$tipo|replace:'_':' '|escape}</span>
                        </div>
                        <p class="descrizione-pubblica">{$l->getDescrizione()|escape}</p>
                        <div class="meta-pubblica">
                            {if $l->getCondominio()}<span>{$l->getCondominio()->getNome()|escape}</span>{/if}
                            <span>{$l->getDataCreazione()->format('d/m/Y')}</span>
                        </div>
                    </article>
                {/foreach}
            </section>
        {/if}
    </main>

</body>
</html>
