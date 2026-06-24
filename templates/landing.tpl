{* templates/landing.tpl *}
{* Landing page d'ingresso di CondoFix (utente non loggato). *}
{* Header con logo + Accedi/Registrati, sezione hero con testo e immagine, *}
{* pulsante "Inizia gratis" (porta alla registrazione amministratore).      *}
{* Variabili dalla View: titolo. *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="pagina-landing">

    {* ---------- HEADER ---------- *}
    <header class="landing-header">
        <div class="landing-logo">
            <img src="img/logo.jpeg" alt="CondoFix">
            <span>CondoFix</span>
        </div>
        <nav class="landing-nav">
            <a href="index.php?action=login" class="btn-secondario">Accedi</a>
            <a href="index.php?action=formRegistrazione" class="btn-primario">Registrati come amministratore</a>
        </nav>
    </header>

    {* ---------- HERO ---------- *}
    <main class="landing-hero">
        <div class="landing-testo">
            <h1>Gestisci il tuo condominio senza pensieri</h1>
            <p class="landing-claim">La gestione condominiale, semplice per davvero.</p>
            <p>
                CondoFix mette in contatto amministratori, condomini e lavoratori
                in un'unica piattaforma. Raccogli le segnalazioni, assegna gli
                interventi ai professionisti giusti e segui ogni lavoro fino alla
                fattura, in totale trasparenza.
            </p>
            <ul class="landing-vantaggi">
                <li>Segnalazioni dei condomini sempre tracciate</li>
                <li>Assegnazione rapida dei lavori ai fornitori</li>
                <li>Storico, foto e fatture per ogni intervento</li>
            </ul>
        </div>

        <div class="landing-immagine">
            <img src="img/hero.jpeg" alt="Anteprima CondoFix">
        </div>
    </main>

    <footer class="landing-footer">
        <p>CondoFix - Gestione interventi condominiali</p>
    </footer>

</body>
</html>







