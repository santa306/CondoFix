{* templates/crea_condominio_admin.tpl *}
{* Form "Nuovo condominio" (Amministratore). *}
{* Variabili dalla View: titolo, errore, successo. *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout-app">

    {include file="_sidebar.tpl"}

    <main class="contenuto">

        <a class="link-indietro" href="index.php?action=listaCondomini">&larr; Torna ai condomini</a>

        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}
        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}

        <h1 class="titolo-pagina">Nuovo condominio</h1>
        <p class="benvenuto">Inserisci i dati del nuovo condominio.</p>

        <div class="form-card">
            <form method="post" action="index.php?action=creaCondominio">

                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required
                       placeholder="Es. Condominio Centrale">

                <label for="indirizzo">Indirizzo</label>
                <input type="text" id="indirizzo" name="indirizzo" required
                       placeholder="Es. Via Roma 10">

                <label for="citta">Città</label>
                <input type="text" id="citta" name="citta" required
                       placeholder="Es. Milano">

                <div class="form-azioni">
                    <a href="index.php?action=listaCondomini" class="btn-secondario">Annulla</a>
                    <button type="submit" class="btn-primario">Crea condominio</button>
                </div>
            </form>
        </div>

    </main>
</div>
</body>
</html>
