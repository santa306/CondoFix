{* templates/cambio_password.tpl *}
{* Cambio password obbligatorio al primo accesso (condòmini e lavoratori). *}
{* Variabili dalla View: titolo, errore. *}
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
            <img src="img/logo.jpeg" alt="CondoFix">
            <span>CondoFix</span>
        </div>
        <nav class="landing-nav">
            <a href="index.php?action=logout" class="btn-secondario">Esci</a>
        </nav>
    </header>

    <main class="contenuto-auth">
        <div class="form-card form-card-auth">
            <h1 class="titolo-pagina">Imposta una nuova password</h1>
            <p class="benvenuto">
                Per la tua sicurezza, al primo accesso devi sostituire la
                password temporanea con una personale.
            </p>

            {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}

            <form method="post" action="index.php?action=cambioPassword">

                <label for="attuale">Password attuale (temporanea)</label>
                <input type="password" id="attuale" name="attuale" required
                       placeholder="La password che ti è stata fornita">

                <label for="nuova">Nuova password</label>
                <input type="password" id="nuova" name="nuova" required
                       placeholder="Almeno 8 caratteri">

                <label for="nuova2">Conferma nuova password</label>
                <input type="password" id="nuova2" name="nuova2" required
                       placeholder="Ripeti la nuova password">

                <div class="form-azioni">
                    <button type="submit" class="btn-primario">Salva nuova password</button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
