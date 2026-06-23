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
<body>
<div class="layout-app">

    {* ===== SIDEBAR ===== *}
    {include file="_sidebar.tpl"}

    {* ===== CONTENUTO ===== *}
    <main class="contenuto">

        <a href="index.php?action=dettaglioIntervento&amp;id={$intervento->getId()}" class="link-indietro">
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
                    Aggiungi foto
                </button>
                <p class="aiuto-campo">Massimo 5 foto in totale — JPG, PNG, WEBP, GIF, max 5 MB.</p>

                <div class="form-azioni">
                    <a href="index.php?action=dettaglioIntervento&amp;id={$intervento->getId()}" class="btn-secondario">Annulla</a>
                    <button type="submit" class="btn-primario">
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

</div>
</body>
</html>

