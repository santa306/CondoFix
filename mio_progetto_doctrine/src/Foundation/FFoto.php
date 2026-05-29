<?php
// src/Foundation/FFoto.php
//
// Gestisce le query relative alle Foto allegate agli interventi.

class FFoto extends FBase
{
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->entityClass = Foto::class;
    }

    // -------------------------------------------------------
    // QUERY SPECIFICHE
    // -------------------------------------------------------

    /**
     * Tutte le foto di un intervento, ordinate per timestamp.
     * Usato nella galleria foto della pagina dettaglio intervento.
     */
    public function findByIntervento(Intervento $intervento): array
    {
        return $this->getRepository()->findBy(
            ['intervento' => $intervento],
            ['timestamp'  => 'ASC']
        );
    }

    /**
     * Numero di foto caricate per un intervento.
     * Usato per mostrare il contatore nella card dell'intervento.
     */
    public function countByIntervento(Intervento $intervento): int
    {
        return (int) $this->em->createQuery('
            SELECT COUNT(f) FROM Foto f
            WHERE f.intervento = :intervento
        ')
        ->setParameter('intervento', $intervento)
        ->getSingleScalarResult();
    }
}
