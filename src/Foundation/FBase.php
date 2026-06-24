<?php
// src/Foundation/FBase.php
//
// Classe ASTRATTA base per tutte le classi Foundation specifiche.
// Fornisce i metodi CRUD generici (exist, load, store, update, delete)
// e mantiene un riferimento all'EntityManager di Doctrine.
//
// Ogni sottoclasse (FIntervento, FUtente, ecc.) estende questa classe
// e aggiunge i metodi di query specifici per la propria Entity.
//
// NON viene usata direttamente dal Control: il Control usa solo
// PersistentManager, che delega alle sottoclassi di FBase.

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

abstract class FBase
{
    // EntityManager condiviso (iniettato dal PersistentManager)
    protected EntityManagerInterface $em;

    // Nome della classe Entity gestita da questa Foundation
    // Viene impostato nel costruttore delle sottoclassi
    protected string $entityClass;//ti dice a quale F* appartiene e viene creato nella soottclasse rispettiva

    // -------------------------------------------------------
    // COSTRUTTORE — riceve l'EntityManager dal PersistentManager
    // -------------------------------------------------------
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // -------------------------------------------------------
    // METODI CRUD GENERICI
    // (ereditati da tutte le sottoclassi)
    // -------------------------------------------------------

    /**
     * Verifica se esiste un record con il dato id.
     */
    public function exist(int $id): bool
    {
        return $this->em->find($this->entityClass, $id) !== null;
    }

    /**
     * Carica un oggetto tramite id. Restituisce null se non trovato.
     */
    public function load(int $id): ?object
    {
        return $this->em->find($this->entityClass, $id);//ci mette la rispettiva entity
    }

    /**
     * Salva un nuovo oggetto nel database (INSERT).
     */
    public function store(object $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * Aggiorna un oggetto già esistente (UPDATE).
     * Modifica i campi con i setter, poi chiama update().
     */
    public function update(): void
    {
        $this->em->flush();
    }

    /**
     * Elimina un oggetto dal database (DELETE).
     */
    public function delete(object $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * Restituisce tutti i record della tabella.
     */
    public function findAll(): array
    {
        return $this->getRepository()->findAll();
    }

    // -------------------------------------------------------
    // HELPER INTERNO
    // -------------------------------------------------------

    /**
     * Restituisce il Repository Doctrine per questa Entity.
     * Usato internamente dalle sottoclassi per le query specifiche.
     */
    protected function getRepository(): EntityRepository
    {
        return $this->em->getRepository($this->entityClass);
    }
}
