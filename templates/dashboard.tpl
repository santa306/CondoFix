{* ============================================================ *}
{* PLACEHOLDER — questa dashboard e' provvisoria.                *}
{* La versione definitiva va costruita secondo gli sketch dello  *}
{* Step 3 (sidebar + card-contatori + sezione "Lavori recenti"). *}
{* E' il primo task di ogni verticale dashboard (admin/condomino/ *}
{* fornitore). Vedi sketch_*.pdf nelle risorse del progetto.     *}
{* ============================================================ *}
{* templates/dashboard.tpl â€” dashboard placeholder per dimostrare il login *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="pagina-dashboard">
        <div class="barra-top">
            <strong>CondoFix</strong>
            <a href="index.php?action=logout">Esci</a>
        </div>

        {if $successo}
            <div class="avviso avviso-successo">{$successo|escape}</div>
        {/if}

        <h1>Dashboard {$ruoloLabel|escape}</h1>
        <p>Accesso effettuato correttamente. Questa pagina e' un segnaposto:
           qui andranno i contenuti specifici del ruolo
           (elenco interventi, azioni, ecc.).</p>
    </div>
</body>
</html>

