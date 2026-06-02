<?php
// src/Control/CDashboard.php
//
// CONTROLLORE minimo delle dashboard (uno per ruolo).
// Per ora sono PLACEHOLDER: servono a completare la verticale del login
// (dopo l'accesso, CLogin reindirizza qui). Le riempirai nello Step 7
// con i dati veri (lista interventi, ecc.) usando lo stesso schema.
//
// Ogni metodo:
//   1. protegge la pagina con Session::requireRole(...)
//   2. delega il disegno a una View
//
// NB: tre dashboard distinte, un solo Control per non moltiplicare i file
//     finche' sono semplici. Quando cresceranno, potrai separarle.

class CDashboard
{
    public function admin(): void
    {
        Session::requireRole('amministratore');
        (new ViewDashboard())->mostra('Amministratore');
    }

    public function fornitore(): void
    {
        Session::requireRole('fornitore');
        (new ViewDashboard())->mostra('Fornitore');
    }

    public function condomino(): void
    {
        Session::requireRole('condomino');
        (new ViewDashboard())->mostra('Condomino');
    }
}
