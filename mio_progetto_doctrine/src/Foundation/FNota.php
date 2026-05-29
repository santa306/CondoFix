<?php
// src/Foundation/FNota.php
//
// Gestisce le query relative alle Note operative degli interventi.

class FNota extends FBase
{
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->entityClass = Nota::class;
    }

    // -------------------------------------------------------
    // QUERY SPECIFICHE
    // -------------------------------------------------------

    /**
     * Tutte le note di un intervento, ordinate per timestamp crescente.
     * Usato nella pagina di dettaglio di un intervento.
     */
    public function findByIntervento(Intervento $intervento): array
    {
        return $this->getRepository()->findBy(
            ['intervento' => $intervento],
            ['timestamp'  => 'ASC']
        );
    }

    /**
     * L'ultima nota aggiunta a un intervento.
     * Usato nel widget di riepilogo della dashboard.
     */
    public function findUltimaByIntervento(Intervento $intervento): ?Nota
    {
        $result = $this->getRepository()->findBy(
            ['intervento' => $intervento],
            ['timestamp'  => 'DESC'],
            1   // limite 1
        );
        return $result[0] ?? null;
    }
}
