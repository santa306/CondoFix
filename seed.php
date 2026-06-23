<?php
require_once "bootstrap.php";

echo "Inizio inserimento dati di test...\n";

try {
    $admin = new Amministratore();
    $admin->setNome("Mario");
    $admin->setCognome("Rossi");
    $admin->setEmail("admin@condofix.it");
    $admin->setPassword("admin123");
    $admin->setTelefono("3331234567");
    $entityManager->persist($admin);

    $condominio = new Condominio();
    $condominio->setNome("Condominio Centrale");
    $condominio->setIndirizzo("Via Roma 10");
    $condominio->setCitta("Torino");
    $condominio->setAmministratore($admin);
    $entityManager->persist($condominio);

    $condomino = new Condomino();
    $condomino->setNome("Luigi");
    $condomino->setCognome("Bianchi");
    $condomino->setEmail("condomino@condofix.it");
    $condomino->setPassword("condomino123");
    $condomino->setCondominio($condominio);
    $condomino->setInterno("Scala A, Int. 5");
    $entityManager->persist($condomino);

    $categoria = new Categoria();
    $categoria->setNome("Idraulico");
    $entityManager->persist($categoria);

    $fornitore = new Fornitore();
    $fornitore->setNome("Giuseppe");
    $fornitore->setCognome("Verdi");
    $fornitore->setEmail("fornitore@condofix.it");
    $fornitore->setPassword("fornitore123");
    $fornitore->setTelefono("3409876543");
    $fornitore->setPartitaIva("12345678901");
    $fornitore->setCategoria($categoria);
    $entityManager->persist($fornitore);

    $entityManager->flush();

    echo "Dati inseriti con successo!\n\n";
    echo "Credenziali per il login:\n";
    echo "  Amministratore -> admin@condofix.it / admin123\n";
    echo "  Condomino      -> condomino@condofix.it / condomino123\n";
    echo "  Fornitore      -> fornitore@condofix.it / fornitore123\n";
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}
