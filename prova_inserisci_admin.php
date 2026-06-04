<?php
// inserisci_admin.php

// 1. Carichiamo la configurazione di Doctrine e la classe Amministratore
require_once "bootstrap.php";
require_once "src/Amministratore.php";

echo "Inizio il test di inserimento...\n";

// 2. Creiamo l'oggetto in PHP (per ora esiste solo nella memoria del computer, non nel database!)
$nuovoAdmin = new Amministratore();
$nuovoAdmin->setNome("Mario");
$nuovoAdmin->setCognome("Rossi");
$nuovoAdmin->setEmail("mario.rossi@email.it");
$nuovoAdmin->setTelefono("3331234567");

// 3. PERSIST: Diciamo a Doctrine "Ehi, tieni d'occhio questo oggetto, voglio salvarlo!"
$entityManager->persist($nuovoAdmin);

// 4. FLUSH: Questo è il vero "INVIA". Prende tutto quello che è in "persist" e lo scrive fisicamente su MySQL.
$entityManager->flush();

// Se arriviamo qui senza errori, il salvataggio è andato a buon fine!
// Nota come Doctrine abbia compilato l'ID in automatico dopo il flush.
echo "Test superato con successo! 🎉\n";
echo "L'amministratore è stato salvato nel database con l'ID numero: " . $nuovoAdmin->getId() . "\n";