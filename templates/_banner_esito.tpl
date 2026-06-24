{* templates/_banner_esito.tpl *}
{* Banner modale di esito (riepilogo azione amministratore). *}
{* Si aspetta la variabile $banner (array) con: tipo, titolo, righe[]. *}
{* Va incluso a fine pagina: {include file="_banner_esito.tpl"} *}
{if $banner}
    {assign var="bannerErrore" value=($banner.tipo == 'errore')}
    <div class="banner-overlay" id="bannerEsito">
        <div class="banner-modale">

            {if isset($banner.foto) && $banner.foto}
            <div class="banner-foto"><img src="{$banner.foto|escape}" alt="Foto profilo"></div>
            {elseif isset($banner.senzaIcona) && $banner.senzaIcona}
            <div class="banner-foto"><div class="avatar avatar-grande"></div></div>
            {else}
            <div class="banner-icona {if $bannerErrore}banner-icona-errore{else}banner-icona-successo{/if}">
                {if $bannerErrore}&#10006;{else}&#10003;{/if}
            </div>
            {/if}

            <h2 class="banner-titolo">{$banner.titolo|escape}</h2>

            {if $banner.sottotitolo}
                <p class="banner-sottotitolo">{$banner.sottotitolo|escape}</p>
            {/if}

            {if $banner.righe}
                <dl class="banner-righe">
                    {foreach $banner.righe as $etichetta => $valore}
                        <div class="banner-riga">
                            <dt>{$etichetta|escape}</dt>
                            <dd>{$valore|escape}</dd>
                        </div>
                    {/foreach}
                </dl>
            {/if}

            <button type="button" class="btn-primario banner-chiudi" onclick="document.getElementById('bannerEsito').remove()">
                Chiudi
            </button>
        </div>
    </div>
{/if}

