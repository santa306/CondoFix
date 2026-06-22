{* templates/presenta_intervento.tpl *}
{* Form "Nuova Segnalazione" — riferimento UI: sketch_condomino.pdf (caso 5). *}
{* Campi: titolo, descrizione, foto (1..5, aggiunte dinamicamente con JS).    *}
{* enctype multipart/form-data per l'upload dei file.                         *}
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

        <header class="intestazione">
            <div>
                <h1>Nuova Segnalazione</h1>
                <p class="benvenuto">Descrivi il problema e allega delle foto se utile.</p>
            </div>
        </header>

        {if $errore}<div class="avviso avviso-errore">{$errore|escape}</div>{/if}
        {if $successo}<div class="avviso avviso-successo">{$successo|escape}</div>{/if}

        <div class="form-card">
            <form method="post" action="index.php?action=presentaIntervento" enctype="multipart/form-data">

                <label for="titolo">Titolo</label>
                <input type="text" id="titolo" name="titolo" maxlength="255"
                       placeholder="Es. Perdita d'acqua nel garage" required>

                <label for="descrizione">Descrizione</label>
                <textarea id="descrizione" name="descrizione" rows="5"
                          placeholder="Descrivi il problema nel dettaglio..." required></textarea>

                <label>Foto (facoltative, max 5)</label>
                <div id="lista-foto">
                    {* Primo campo, sempre presente *}
                    <div class="riga-foto">
                        <input type="file" name="foto[]" accept="image/*">
                    </div>
                </div>

                <button type="button" id="btn-aggiungi-foto" class="btn-aggiungi">
                    Aggiungi foto
                </button>
                <p class="aiuto-campo">Formati ammessi: JPG, PNG, WEBP, GIF — max 5 MB ciascuna.</p>

                <div class="form-azioni">
                    <a href="index.php?action=dashboardCondomino" class="btn-secondario">Annulla</a>
                    <button type="submit" class="btn-primario">
                        Invia Segnalazione
                    </button>
                </div>

            </form>
        </div>

    </main>

    {* ===== JS: aggiunta/rimozione dinamica dei campi foto (max 5) ===== *}
    <script>
        (function () {
            var MAX = 5;
            var lista = document.getElementById('lista-foto');
            var btn   = document.getElementById('btn-aggiungi-foto');

            function aggiornaBottone() {
                var righe = lista.querySelectorAll('.riga-foto').length;
                btn.style.display = (righe >= MAX) ? 'none' : '';
            }

            btn.addEventListener('click', function () {
                if (lista.querySelectorAll('.riga-foto').length >= MAX) return;

                var riga = document.createElement('div');
                riga.className = 'riga-foto';

                var input = document.createElement('input');
                input.type = 'file';
                input.name = 'foto[]';
                input.accept = 'image/*';

                var rimuovi = document.createElement('button');
                rimuovi.type = 'button';
                rimuovi.className = 'btn-rimuovi';
                rimuovi.textContent = '\u00D7'; // ×
                rimuovi.title = 'Rimuovi';
                rimuovi.addEventListener('click', function () {
                    riga.remove();
                    aggiornaBottone();
                });

                riga.appendChild(input);
                riga.appendChild(rimuovi);
                lista.appendChild(riga);
                aggiornaBottone();
            });
        })();
    </script>

</div>
</body>
</html>

