<?php
// prova_foundation.php
// Script di test che dimostra l'uso del nuovo strato Foundation.
// Eseguire DOPO crea_tabelle.php:
//   php prova_foundation.php

require_once "bootstrap.php";

// Carico le classi Foundation (l'autoload classmap le trova,
// ma se non hai rifatto "composer dump-autoload" includile a mano)
require_once "src/Foundation/PersistentManager.php";
require_once "src/Foundation/FBase.php";
require_once "src/Foundation/FUtente.php";

echo "=== TEST FOUNDATION ===\n";

$pm = PersistentManager::getInstance();

// 1. Creo un amministratore
$admin = new Amministratore();
$admin->setNome("Mario");
$admin->setCognome("Rossi");
$admin->setEmail("mario.rossi@email.it");
$admin->setPassword("password123");   // verrà hashata automaticamente
$admin->setTelefono("3331234567");

$pm->store($admin);
echo "Amministratore salvato con id: " . $admin->getId() . "\n";

// 2. Test del login tramite Foundation
$utenteLoggato = $pm->utente()->login("mario.rossi@email.it", "password123");
if ($utenteLoggato !== null) {
    echo "Login riuscito! Benvenuto " . $utenteLoggato->getNome() . "\n";
} else {
    echo "Login fallito.\n";
}

// 3. Test login con password sbagliata
$test = $pm->utente()->login("mario.rossi@email.it", "passwordSbagliata");
echo $test === null ? "Password errata correttamente rifiutata.\n" : "ERRORE: ha accettato password sbagliata!\n";

echo "=== FINE TEST ===\n";
