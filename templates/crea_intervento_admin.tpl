{* templates/crea_intervento_admin.tpl *}
{* Form "Nuovo lavoro" (Amministratore) — struttura unificata. *}
{* L'admin crea un lavoro gia' Accettato: sceglie condominio, fornitore, priorita'. *}
{* Variabili dalla View: titolo, condomini[], fornitori[], errore, successo. *}
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

    <aside class="sidebar">
        <div class="sidebar-logo"><img src="img/logo.jpeg" alt="CondoFix"><span>CondoFix</span></div>
        <nav class="sidebar-menu">
            <a class="voce" href="index.php?action=dashboardAdmin">Dashboard</a>
            <a class="voce attiva" href="index.php?action=formCreaIntervento">Nuovo lavoro</a>
            <a class="voce logout" href="index.php?action=logout">Esci</a>
        </nav>
    </aside>

    <main class="contenuto">

        <a class="link-indietro" href="index.php?action=dashboardAdmin">&larr; Torna alla dashboard</a>

        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}
        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}

        <h1 class="titolo-pagina">Nuovo lavoro</h1>
        <p class="benvenuto">Crea un lavoro e assegnalo subito a un fornitore.</p>

        <div class="form-card">
            <form method="post" action="index.php?action=creaIntervento">

                <label for="titolo">Titolo</label>
                <input type="text" id="titolo" name="titolo" required
                       placeholder="Es. Sostituzione lampadina scala B">

                <label for="descrizione">Descrizione</label>
                <textarea id="descrizione" name="descrizione" rows="5" required
                          placeholder="Descrivi il lavoro da svolgere..."></textarea>

                <label for="id_condominio">Condominio</label>
                <select id="id_condominio" name="id_condominio" required>
                    <option value="">Seleziona…</option>
                    {foreach $condomini as $c}
                        <option value="{$c->getId()}">{$c->getNome()|escape}</option>
                    {/foreach}
                </select>

                <label for="priorita">Priorità</label>
                <select id="priorita" name="priorita" required>
                    <option value="">Seleziona…</option>
                    <option value="alta">Alta</option>
                    <option value="media">Media</option>
                    <option value="bassa">Bassa</option>
                </select>

                <label for="id_fornitore">Fornitore</label>
                <select id="id_fornitore" name="id_fornitore" required>
                    <option value="">Seleziona…</option>
                    {foreach $fornitori as $f}
                        <option value="{$f->getId()}">
                            {$f->getNome()|escape} {$f->getCognome()|escape}{if $f->getCategoria()} — {$f->getCategoria()->getNome()|escape}{/if}
                        </option>
                    {/foreach}
                </select>

                <div class="form-azioni">
                    <a href="index.php?action=dashboardAdmin" class="btn-secondario">Annulla</a>
                    <button type="submit" class="btn-primario">Crea lavoro</button>
                </div>
            </form>
        </div>

    </main>
</div>
</body>
</html>

