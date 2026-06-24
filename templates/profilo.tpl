{* templates/profilo.tpl *}
{* Profilo personale: dati in sola lettura, foto cliccabile, cambio password. *}
{* Variabili: titolo, utente, ruolo, successo, errore. *}
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

        <a class="link-indietro" href="javascript:history.back()">&larr; Indietro</a>

        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}
        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}

        <h1 class="titolo-pagina">Il mio profilo</h1>

        {* --- Dati personali + foto cliccabile --- *}
        <section class="scheda profilo-testa">

            {* Il cerchio è cliccabile: apre il selettore file, che invia da solo. *}
            <form method="post" action="index.php?action=profiloFoto" enctype="multipart/form-data" id="formFoto">
                <label class="profilo-avatar" title="Clicca per cambiare foto">
                    {if $utente->getFotoProfilo()}
                        <img src="{$utente->getFotoProfilo()|escape}" alt="Foto profilo">
                    {else}
                        <div class="avatar avatar-grande"></div>
                    {/if}
                    <span class="profilo-avatar-overlay">Cambia foto</span>
                    <input type="file" name="foto" accept="image/*" hidden
                           onchange="document.getElementById('formFoto').submit()">
                </label>
            </form>

            <div class="profilo-dati">
                <div class="riga-dato"><span class="etichetta-dato">Nome</span><span>{$utente->getNome()|escape} {$utente->getCognome()|escape}</span></div>
                <div class="riga-dato"><span class="etichetta-dato">Email</span><span>{$utente->getEmail()|escape}</span></div>

                {if $ruolo == 'amministratore'}
                    <div class="riga-dato"><span class="etichetta-dato">Ruolo</span><span>Amministratore</span></div>
                    <div class="riga-dato"><span class="etichetta-dato">Telefono</span><span>{if $utente->getTelefono()}{$utente->getTelefono()|escape}{else}—{/if}</span></div>
                {elseif $ruolo == 'condomino'}
                    <div class="riga-dato"><span class="etichetta-dato">Ruolo</span><span>Condomino</span></div>
                    <div class="riga-dato"><span class="etichetta-dato">Condominio</span><span>{if $utente->getCondominio()}{$utente->getCondominio()->getNome()|escape}{else}—{/if}</span></div>
                    <div class="riga-dato"><span class="etichetta-dato">Interno</span><span>{if $utente->getInterno()}{$utente->getInterno()|escape}{else}—{/if}</span></div>
                {elseif $ruolo == 'fornitore'}
                    <div class="riga-dato"><span class="etichetta-dato">Ruolo</span><span>Lavoratore</span></div>
                    <div class="riga-dato"><span class="etichetta-dato">Telefono</span><span>{if $utente->getTelefono()}{$utente->getTelefono()|escape}{else}—{/if}</span></div>
                    <div class="riga-dato"><span class="etichetta-dato">Partita IVA</span><span>{if $utente->getPartitaIva()}{$utente->getPartitaIva()|escape}{else}—{/if}</span></div>
                    <div class="riga-dato"><span class="etichetta-dato">Categoria</span><span>{if $utente->getCategoria()}{$utente->getCategoria()->getNome()|escape}{else}—{/if}</span></div>
                {/if}

                <div class="riga-dato"><span class="etichetta-dato">Password</span><span>&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span></div>
            </div>
        </section>

        {* --- Link per cambiare password (fuori dal riquadro dati) --- *}
        <p class="profilo-cambio-pw">
            Vuoi cambiare la password?
            <a href="javascript:void(0)" onclick="document.getElementById('boxPassword').classList.toggle('aperto')">Clicca qua</a>
        </p>

        {* --- Form password, nascosto finché non si clicca il link --- *}
        <section class="scheda box-password" id="boxPassword">
            <h2>Cambia password</h2>
            <form method="post" action="index.php?action=profiloPassword">
                <label for="attuale">Password attuale</label>
                <input type="password" id="attuale" name="attuale" required>

                <label for="nuova">Nuova password</label>
                <input type="password" id="nuova" name="nuova" required placeholder="Almeno 8 caratteri">

                <label for="nuova2">Conferma nuova password</label>
                <input type="password" id="nuova2" name="nuova2" required>

                <div class="form-azioni">
                    <button type="submit" class="btn-primario">Aggiorna password</button>
                </div>
            </form>
        </section>

    </main>
</div>
</body>
</html>
