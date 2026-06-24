{* templates/crea_lavoratore.tpl *}
{* Form "Crea lavoratore" (Amministratore). *}
{* Variabili: titolo, categorie[], errore. *}
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

        <a class="link-indietro" href="index.php?action=listaLavoratori">&larr; Torna ai lavoratori</a>

        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}

        <h1 class="titolo-pagina">Nuovo lavoratore</h1>
        <p class="benvenuto">Crea un lavoratore. Riceverà una password temporanea da cambiare al primo accesso.</p>

        <div class="form-card">
            <form method="post" action="index.php?action=creaLavoratore">

                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required placeholder="Es. Giuseppe">

                <label for="cognome">Cognome</label>
                <input type="text" id="cognome" name="cognome" required placeholder="Es. Verdi">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="nome@esempio.it">

                <label for="telefono">Telefono (opzionale)</label>
                <input type="text" id="telefono" name="telefono" placeholder="Es. 333 1234567">

                <label for="partitaIva">Partita IVA (opzionale)</label>
                <input type="text" id="partitaIva" name="partitaIva" placeholder="Es. 01234567890">

                <label for="categoria">Categoria / Specializzazione</label>
                <select id="categoria" name="categoria">
                    <option value="">— Seleziona —</option>
                    {foreach $categorie as $cat}
                        <option value="{$cat->getId()}">{$cat->getNome()|escape}</option>
                    {/foreach}
                </select>

                <label for="nuovaCategoria">…oppure scrivi una nuova categoria</label>
                <input type="text" id="nuovaCategoria" name="nuovaCategoria" placeholder="Es. Idraulico, Giardiniere…">

                <label for="password">Password temporanea</label>
                <input type="text" id="password" name="password" required placeholder="Almeno 8 caratteri">

                <div class="form-azioni">
                    <a href="index.php?action=listaLavoratori" class="btn-secondario">Annulla</a>
                    <button type="submit" class="btn-primario">Crea lavoratore</button>
                </div>
            </form>
        </div>

    </main>
</div>
</body>
</html>
