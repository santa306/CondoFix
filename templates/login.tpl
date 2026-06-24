{* templates/login.tpl *}
{* Template Smarty della pagina di login.                                  *}
{* Sintassi Smarty 5:                                                      *}
{*   {$variabile}            -> stampa una variabile passata dalla View    *}
{*   {if ...}{/if}           -> condizione                                 *}
{*   {$var|escape}           -> escaping HTML (sicurezza anti-XSS)         *}
{* La View passa: titolo, errore, successo.                                *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="pagina-login">

    <header class="landing-header">
        <div class="landing-logo">
            <a href="index.php?action=landing" style="display:flex;align-items:center;gap:0.6rem;text-decoration:none;">
                <img src="img/logo.jpeg" alt="CondoFix">
                <span>CondoFix</span>
            </a>
        </div>
        <nav class="landing-nav">
            <a href="index.php?action=formRegistrazione" class="btn-primario">Registrati</a>
        </nav>
    </header>

    <main class="card-login">
        <div class="logo-login"><img src="img/logo.jpeg" alt="CondoFix"><h1>CondoFix</h1></div>
        <p class="sottotitolo">Gestione interventi condominiali</p>

        {* --- Messaggi flash --- *}
        {if $errore}
            <div class="avviso avviso-errore">{$errore|escape}</div>
        {/if}
        {if $successo}
            <div class="avviso avviso-successo">{$successo|escape}</div>
        {/if}

        {* --- Form di login ---                                            *}
        {* Invia in POST all'azione doLogin del front controller.           *}
        <form method="post" action="index.php?action=doLogin" class="form-login">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   placeholder="nome@esempio.it" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="La tua password" required>

            <button type="submit" class="btn-accedi">Accedi</button>
        </form>
        <a href="index.php?action=vetrina" class="btn-esplora">Esplora senza accedere</a>
    </main>

</body>
</html>



