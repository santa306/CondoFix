<?php
// src/View/ViewBase.php
//
// RUOLO NELLO STRATO PRESENTATION:
//   Classe BASE di tutte le View. Incapsula Smarty: configura una volta sola
//   le cartelle dei template e della cache, ed espone metodi semplici che le
//   View concrete useranno (assign + display).
//
//   Cosi' ogni View concreta (ViewLogin, ViewDashboardAdmin, ...) non deve
//   riconfigurare Smarty: eredita tutto da qui.
//
// IMPORTANTE — SMARTY 5.x:
//   In Smarty 5 la classe e' nel namespace \Smarty\Smarty (NON piu' \Smarty
//   come in Smarty 4). Per questo sotto si usa "new \Smarty\Smarty()".
//
// Le classi del progetto sono nel namespace globale: nessun "namespace" qui.

abstract class ViewBase
{
    // Istanza di Smarty condivisa da questa View
    protected \Smarty\Smarty $smarty;

    public function __construct()
    {
        // Smarty viene caricato da Composer (vendor/autoload.php, gia' incluso
        // da bootstrap.php nel front controller).
        $this->smarty = new \Smarty\Smarty();

        // Percorsi assoluti calcolati a partire da questo file.
        // __DIR__ = .../src/View  ->  saliamo di due livelli alla root progetto.
        $root = __DIR__ . '/../..';

        // Dove stanno i file .tpl (i template scritti da noi)
        $this->smarty->setTemplateDir($root . '/templates');

        // Dove Smarty mette i template compilati (cache tecnica).
        // Questa cartella va in .gitignore: si rigenera da sola.
        $this->smarty->setCompileDir($root . '/templates_c');
    }

    /**
     * Passa una variabile al template.
     * Uso (dalla View concreta): $this->assign('titolo', 'Login');
     */
    protected function assign(string $nome, mixed $valore): void
    {
        $this->smarty->assign($nome, $valore);
    }

    /**
     * Rende (stampa) un template .tpl gia' popolato con le variabili.
     * Uso: $this->render('login.tpl');
     */
    protected function render(string $template): void
    {
        // Variabili della sidebar comuni a tutte le pagine: lette dalla Session,
        // cosi' la barra laterale e' SEMPRE identica e completa (nome, ruolo,
        // voci di menu) su ogni pagina, senza che ogni View le debba passare.
        $this->smarty->assign('sidebarNome',  Session::getNomeCompleto());
        $this->smarty->assign('sidebarRuolo', Session::getRuolo());
        $this->smarty->assign('sidebarRuoloLabel', Session::getRuoloLabel());
        $this->smarty->assign('sidebarFoto', Session::get('fotoProfilo'));
        // Azione corrente: serve al partial per evidenziare la voce attiva.
        $this->smarty->assign('sidebarAzione', $_GET['action'] ?? '');

        $this->smarty->display($template);
    }

    /**
     * Helper: legge un campo POST in modo sicuro (stringa, mai null).
     * Le View sono l'UNICO punto che tocca $_POST/$_GET: i Control no.
     */
    protected function post(string $campo): string
    {
        return isset($_POST[$campo]) ? trim((string) $_POST[$campo]) : '';
    }

    /**
     * Helper: legge un campo GET in modo sicuro (stringa, mai null).
     */
    protected function get(string $campo): string
    {
        return isset($_GET[$campo]) ? trim((string) $_GET[$campo]) : '';
    }
}
