{* templates/vetrina_dettaglio.tpl *}
{* Dettaglio pubblico di un lavoro DIMOSTRATIVO (Utente non registrato). *}
{* Dati di esempio (array), non dal database. Variabili: titolo, lavoro[]. *}
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

        <a class="link-indietro" href="index.php?action=vetrina">&larr; Torna ai lavori</a>

        <div class="dettaglio-testa">
            <h1>{$lavoro.titolo|escape}</h1>
            <span class="badge badge-{$lavoro.stato|escape}">{$lavoro.stato|replace:'_':' '|escape}</span>
        </div>

        <section class="scheda">
            <div class="riga-dato">
                <span class="etichetta-dato">Condominio</span>
                <span>{$lavoro.condominio|escape}</span>
            </div>
            <div class="riga-dato">
                <span class="etichetta-dato">Data</span>
                <span>{$lavoro.data|escape}</span>
            </div>
            <div class="riga-dato">
                <span class="etichetta-dato">Stato</span>
                <span>{$lavoro.stato|replace:'_':' '|escape}</span>
            </div>
        </section>

        <section class="scheda">
            <h2>Descrizione</h2>
            <p class="testo-descrizione">{$lavoro.descrizione|escape}</p>
        </section>

        <p class="nota-demo">Questo e' un lavoro di esempio. <a href="index.php?action=login">Accedi</a> per gestire lavori reali.</p>

    </main>

</body>
</html>
