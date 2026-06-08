{* templates/modifica_intervento.tpl *}
{* Modifica segnalazione (caso 10) — descrizione + gestione foto.            *}
{* La View passa: intervento, foto[], errore, successo.                      *}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titolo|escape}</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="app">

    {* ===== SIDEBAR ===== *}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9v.01"/><path d="M9 12v.01"/><path d="M9 15v.01"/></svg>
            <span>CondoFix</span>
        </div>
        <nav class="sidebar-menu">
            <a href="index.php?action=dashboardCondomino" class="voce-menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
        </nav>
        <a href="index.php?action=logout" class="voce-menu voce-esci">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Esci
        </a>
    </aside>

    {* ===== CONTENUTO ===== *}
    <main class="contenuto">

        <a href="index.php?action=dettaglioIntervento&amp;id={$intervento->getId()}" class="link-indietro">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Torna al dettaglio
        </a>

        <header class="intestazione">
            <div>
                <h1>Modifica Segnalazione</h1>
                <p class="benvenuto">{$intervento->getTitolo()|escape}</p>
            </div>
        </header>

        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}
        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}

        <div class="form-card">
            <form method="post" action="index.php?action=modificaIntervento&amp;id={$intervento->getId()}" enctype="multipart/form-data">

                <label for="descrizione">Descrizione</label>
                <textarea id="descrizione" name="descrizione" rows="5" required>{$intervento->getDescrizione()|escape}</textarea>

                {* --- Foto esistenti, con checkbox per eliminarle --- *}
                <label>Foto attuali</label>
                {if $foto|@count == 0}
                    <p class="aiuto-campo">Nessuna foto allegata.</p>
                {else}
                    <div class="galleria-modifica">
                        {foreach $foto as $f}
                            <label class="foto-eliminabile">
                                <img src="{$f->getPercorso()|escape}" alt="{$f->getNomeOriginale()|escape}">
                                <span class="checkbox-elimina">
                                    <input type="checkbox" name="elimina_foto[]" value="{$f->getId()}">
                                    Elimina
                                </span>
                            </label>
                        {/foreach}
                    </div>
                    <p class="aiuto-campo">Spunta le foto che vuoi rimuovere.</p>
                {/if}

                {* --- Aggiunta nuove foto (dinamico) --- *}
                <label>Aggiungi foto</label>
                <div id="lista-foto">
                    <div class="riga-foto"><input type="file" name="foto[]" accept="image/*"></div>
                </div>
                <button type="button" id="btn-aggiungi-foto" class="btn-aggiungi">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Aggiungi foto
                </button>
                <p class="aiuto-campo">Massimo 5 foto in totale — JPG, PNG, WEBP, GIF, max 5 MB.</p>

                <div class="form-azioni">
                    <a href="index.php?action=dettaglioIntervento&amp;id={$intervento->getId()}" class="btn-secondario">Annulla</a>
                    <button type="submit" class="btn-primario">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Salva modifiche
                    </button>
                </div>

            </form>
        </div>

    </main>

    <script>
        (function () {
            var MAX = 5;
            var lista = document.getElementById('lista-foto');
            var btn   = document.getElementById('btn-aggiungi-foto');
            function aggiorna() {
                var n = lista.querySelectorAll('.riga-foto').length;
                btn.style.display = (n >= MAX) ? 'none' : '';
            }
            btn.addEventListener('click', function () {
                if (lista.querySelectorAll('.riga-foto').length >= MAX) return;
                var riga = document.createElement('div');
                riga.className = 'riga-foto';
                var input = document.createElement('input');
                input.type = 'file'; input.name = 'foto[]'; input.accept = 'image/*';
                var rim = document.createElement('button');
                rim.type = 'button'; rim.className = 'btn-rimuovi'; rim.textContent = '\u00D7';
                rim.addEventListener('click', function () { riga.remove(); aggiorna(); });
                riga.appendChild(input); riga.appendChild(rim);
                lista.appendChild(riga); aggiorna();
            });
        })();
    </script>

</body>
</html>
