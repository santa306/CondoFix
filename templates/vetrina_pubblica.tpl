{* templates/vetrina_pubblica.tpl *}
{* Vetrina pubblica (Utente non registrato): elenco lavori DIMOSTRATIVI. *}
{* I lavori sono dati di esempio (array), non dal database. Ogni lavoro e' *}
{* cliccabile e apre il dettaglio pubblico. Variabili: titolo, lavori[]. *}
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
        <h1 class="titolo-pagina">Scopri come funziona CondoFix</h1>
        <p class="benvenuto">Questi sono alcuni lavori di esempio. Clicca su un lavoro per vederne i dettagli. Per gestire lavori reali, effettua l'accesso.</p>

        <section class="lista-pubblica">
            {foreach $lavori as $l}
                <a class="card-lavoro-pubblica" href="index.php?action=vetrinaDettaglio&id={$l.id|escape}">
                    <div class="riga-titolo">
                        <h2>{$l.titolo|escape}</h2>
                        <span class="badge badge-{$l.stato|escape}">{$l.stato|replace:'_':' '|escape}</span>
                    </div>
                    <p class="descrizione-pubblica">{$l.descrizione|truncate:110|escape}</p>
                    <div class="meta-pubblica">
                        <span>{$l.condominio|escape}</span>
                        <span>{$l.data|escape}</span>
                        <span class="vedi-dettaglio">Vedi dettaglio &rarr;</span>
                    </div>
                </a>
            {/foreach}
        </section>
    </main>

</body>
</html>
