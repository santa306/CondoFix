{* templates/_sidebar.tpl *}
{* Sidebar UNICA e identica su tutte le pagine, per tutti i ruoli. *}
{* Legge i dati dalla Session tramite le variabili assegnate da ViewBase::render(): *}
{*   $sidebarNome, $sidebarRuolo, $sidebarRuoloLabel, $sidebarAzione *}
{* La voce "attiva" viene evidenziata confrontando l'azione corrente. *}
<aside class="sidebar">
    <div class="sidebar-logo"><img src="img/logo.jpeg" alt="CondoFix"><span>CondoFix</span></div>

    <a class="sidebar-utente" href="index.php?action=profilo" title="Il mio profilo">
        {if $sidebarFoto}
            <div class="avatar avatar-foto"><img src="{$sidebarFoto|escape}" alt="Profilo"></div>
        {else}
            <div class="avatar"></div>
        {/if}
        <div>
            <div class="nome">{$sidebarNome|escape}</div>
            <div class="ruolo">{$sidebarRuoloLabel|escape}</div>
        </div>
    </a>

    <nav class="sidebar-menu">
        {if $sidebarRuolo == 'amministratore'}
            <a class="voce {if $sidebarAzione == 'dashboardAdmin'}attiva{/if}" href="index.php?action=dashboardAdmin">Dashboard</a>
            <a class="voce {if $sidebarAzione == 'formCreaIntervento' || $sidebarAzione == 'creaIntervento'}attiva{/if}" href="index.php?action=formCreaIntervento">Nuovo lavoro</a>
            <a class="voce {if $sidebarAzione == 'listaLavoratori'}attiva{/if}" href="index.php?action=listaLavoratori">Lavoratori</a>
            <a class="voce {if $sidebarAzione == 'listaCondomini' || $sidebarAzione == 'formCreaCondominio' || $sidebarAzione == 'creaCondominio'}attiva{/if}" href="index.php?action=listaCondomini">Condomini</a>

        {elseif $sidebarRuolo == 'condomino'}
            <a class="voce {if $sidebarAzione == 'dashboardCondomino'}attiva{/if}" href="index.php?action=dashboardCondomino">Dashboard</a>
            <a class="voce {if $sidebarAzione == 'formPresentaIntervento' || $sidebarAzione == 'presentaIntervento'}attiva{/if}" href="index.php?action=formPresentaIntervento">Nuova segnalazione</a>

        {elseif $sidebarRuolo == 'fornitore'}
            <a class="voce {if $sidebarAzione == 'dashboardFornitore'}attiva{/if}" href="index.php?action=dashboardFornitore">I miei lavori</a>
        {/if}

        <a class="voce logout" href="index.php?action=logout">Esci</a>
    </nav>
</aside>
