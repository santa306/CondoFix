{* templates/crea_condomino.tpl *}
{* Form "Aggiungi condòmino" a un condominio. *}
{* Variabili: titolo, condominio, errore. *}
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

        <a class="link-indietro" href="index.php?action=dettaglioCondominio&id={$condominio->getId()}">&larr; Torna al condominio</a>

        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}

        <h1 class="titolo-pagina">Aggiungi condòmino</h1>
        <p class="benvenuto">Nuovo condòmino per <strong>{$condominio->getNome()|escape}</strong>. Riceverà una password temporanea da cambiare al primo accesso.</p>

        <div class="form-card">
            <form method="post" action="index.php?action=creaCondomino">
                <input type="hidden" name="idCondominio" value="{$condominio->getId()}">

                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required placeholder="Es. Luigi">

                <label for="cognome">Cognome</label>
                <input type="text" id="cognome" name="cognome" required placeholder="Es. Bianchi">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="nome@esempio.it">

                <label for="interno">Interno / Appartamento (opzionale)</label>
                <input type="text" id="interno" name="interno" placeholder="Es. Scala A, Int. 5">

                <label for="password">Password temporanea</label>
                <input type="text" id="password" name="password" required placeholder="Almeno 8 caratteri">

                <div class="form-azioni">
                    <a href="index.php?action=dettaglioCondominio&id={$condominio->getId()}" class="btn-secondario">Annulla</a>
                    <button type="submit" class="btn-primario">Crea condòmino</button>
                </div>
            </form>
        </div>

    </main>
</div>
</body>
</html>
