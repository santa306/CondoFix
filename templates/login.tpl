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

    <main class="card-login">
        <h1 class="logo">CondoFix</h1>
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
    </main>

</body>
</html>
