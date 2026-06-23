<?php
// src/Foundation/FCategoria.php
//
// Gestisce le query relative alle Categorie dei fornitori.
// Semplice: quasi sempre si usa solo findAll() per popolare i select.

class FCategoria extends FBase
{
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->entityClass = Categoria::class;
    }

    // -------------------------------------------------------
    // QUERY SPECIFICHE
    // -------------------------------------------------------

    /**
     * Tutte le categorie ordinate alfabeticamente.
     * Usato nei form per popolare il select "Tipo intervento".
     */
    public function findAll(): array
    {
        return $this->getRepository()->findBy([], ['nome' => 'ASC']);
    }

    /**
     * Cerca una categoria per nome esatto.
     * Usato per evitare duplicati durante la creazione.
     */
    public function findByNome(string $nome): ?Categoria
    {
        return $this->getRepository()->findOneBy(['nome' => $nome]);
    }

    /**
     * Verifica se esiste già una categoria con questo nome.
     */
    public function esisteNome(string $nome): bool
    {
        return $this->findByNome($nome) !== null;
    }
}
