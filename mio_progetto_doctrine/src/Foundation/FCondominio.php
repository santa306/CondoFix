<?php
// src/Foundation/FCondominio.php
//
// Gestisce tutte le query relative ai Condomini.

class FCondominio extends FBase
{
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->entityClass = Condominio::class;
    }

    // -------------------------------------------------------
    // QUERY SPECIFICHE
    // -------------------------------------------------------

    /**
     * Tutti i condomini gestiti da un amministratore specifico.
     * Usato nella sidebar/menu dell'Admin per elencare i suoi condomini.
     */
    public function findByAmministratore(Amministratore $admin): array
    {
        return $this->getRepository()->findBy(
            ['amministratore' => $admin],
            ['nome' => 'ASC']
        );
    }

    /**
     * Cerca un condominio per nome (ricerca parziale).
     * Usato in eventuali funzionalità di ricerca.
     */
    public function findByNome(string $nome): array
    {
        return $this->em->createQuery('
            SELECT c FROM Condominio c
            WHERE c.nome LIKE :nome
            ORDER BY c.nome ASC
        ')
        ->setParameter('nome', '%' . $nome . '%')
        ->getResult();
    }
}
