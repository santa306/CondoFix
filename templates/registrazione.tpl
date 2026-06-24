{* templates/registrazione.tpl *}
{* Registrazione di un nuovo Amministratore di condominio. *}
{* Pagina pubblica (utente non loggato). *}
{* Variabili dalla View: titolo, errore, e i valori già inseriti (vecchi)   *}
{* per non far riscrivere tutto in caso di errore: vNome, vCognome, ...     *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="pagina-landing">

    <header class="landing-header">
        <div class="landing-logo">
            <a href="index.php?action=landing" style="display:flex;align-items:center;gap:0.6rem;text-decoration:none;">
                <img src="img/logo.jpeg" alt="CondoFix">
                <span>CondoFix</span>
            </a>
        </div>
        <nav class="landing-nav">
            <a href="index.php?action=login" class="btn-secondario">Hai già un account? Accedi</a>
        </nav>
    </header>

    <main class="contenuto-auth">
        <div class="form-card form-card-auth">
            <h1 class="titolo-pagina">Registrati come amministratore</h1>
            <p class="benvenuto">Crea il tuo account per gestire condomìni e interventi.</p>

            {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}

            <form method="post" action="index.php?action=registraAdmin">

                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="{$vNome|escape}" required placeholder="Es. Mario">

                <label for="cognome">Cognome</label>
                <input type="text" id="cognome" name="cognome" value="{$vCognome|escape}" required placeholder="Es. Rossi">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{$vEmail|escape}" required placeholder="nome@esempio.it">

                <label for="telefono">Telefono (opzionale)</label>
                <input type="text" id="telefono" name="telefono" value="{$vTelefono|escape}" placeholder="Es. 333 1234567">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Almeno 8 caratteri">

                <label for="password2">Conferma password</label>
                <input type="password" id="password2" name="password2" required placeholder="Ripeti la password">

                <div class="form-azioni">
                    <a href="index.php?action=landing" class="btn-secondario">Annulla</a>
                    <button type="submit" class="btn-primario">Crea account</button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
