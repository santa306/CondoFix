<?php
// src/Foundation/FIntervento.php
//
// Gestisce tutte le query relative agli Interventi.
// Centralizza qui la logica di accesso ai dati degli interventi,
// evitando query sparse nei Control.

use Doctrine\ORM\Query\Expr\Join;

class FIntervento extends FBase
{
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->entityClass = Intervento::class;
    }

    // -------------------------------------------------------
    // QUERY PER CONDOMINIO
    // -------------------------------------------------------

    /**
     * Tutti gli interventi di un condominio specifico.
     * Usato nella dashboard del Condomino e dell'Admin.
     */
    public function findByCondominio(Condominio $condominio): array
    {
        return $this->getRepository()->findBy(
            ['condominio' => $condominio],
            ['dataCreazione' => 'DESC']
        );
    }

    /**
     * Tutti gli interventi di un condominio filtrati per tipo di stato.
     * Es: tutti i "presentato" del Condominio X.
     *
     * @param string $tipoStato  'presentato' | 'negato' | 'accettato' | 'in_corso' | 'completato'
     */
    public function findByCondominioAndStato(Condominio $condominio, string $tipoStato): array
    {
        // Usiamo DQL perché dobbiamo filtrare sul discriminator della tabella Stato
        return $this->em->createQuery('
            SELECT i FROM Intervento i
            JOIN i.stato s
            WHERE i.condominio = :condominio
            AND s INSTANCE OF :tipo
            ORDER BY i.dataCreazione DESC
        ')
        ->setParameter('condominio', $condominio)
        ->setParameter('tipo', $tipoStato)
        ->getResult();
    }

    // -------------------------------------------------------
    // QUERY PER STATO
    // -------------------------------------------------------

    /**
     * Tutti gli interventi in stato "Presentato" (segnalazioni da valutare).
     * Usato nella dashboard Admin per mostrare le segnalazioni in attesa.
     */
    public function findPresentati(): array
    {
        return $this->em->createQuery('
            SELECT i FROM Intervento i JOIN i.stato s
            WHERE s INSTANCE OF ' . Presentato::class . '
            ORDER BY i.dataCreazione ASC
        ')->getResult();
    }

    /**
     * Tutti gli interventi assegnati a un fornitore specifico.
     * Usato nella dashboard del Fornitore (mostra solo i suoi lavori).
     */
    public function findByFornitore(Fornitore $fornitore): array
    {
        return $this->em->createQuery('
            SELECT i FROM Intervento i JOIN i.stato s
            WHERE s.fornitore = :fornitore
            ORDER BY i.dataCreazione DESC
        ')
        ->setParameter('fornitore', $fornitore)
        ->getResult();
    }

    /**
     * Tutti gli interventi attivi (accettati + in corso) di un fornitore.
     * Usato nella lista lavori attivi del fornitore.
     */
    public function findAttiviByFornitore(Fornitore $fornitore): array
    {
        return $this->em->createQuery('
            SELECT i FROM Intervento i JOIN i.stato s
            WHERE s.fornitore = :fornitore
            AND (s INSTANCE OF ' . Accettato::class . '
                 OR s INSTANCE OF ' . InCorso::class . ')
            ORDER BY i.dataCreazione DESC
        ')
        ->setParameter('fornitore', $fornitore)
        ->getResult();
    }

    /**
     * Tutti gli interventi raggruppati per tipo di stato.
     * Usato nella dashboard Admin per le card con i contatori.
     * Restituisce: ['presentato' => [...], 'accettato' => [...], ...]
     */
    public function findGroupedByStato(): array
    {
        $tutti = $this->getRepository()->findBy([], ['dataCreazione' => 'DESC']);
        $grouped = [
            'presentato' => [],
            'negato'     => [],
            'accettato'  => [],
            'in_corso'   => [],
            'completato' => [],
        ];
        foreach ($tutti as $intervento) {
            $tipo = $intervento->getStato()?->getTipo();
            if (isset($grouped[$tipo])) {
                $grouped[$tipo][] = $intervento;
            }
        }
        return $grouped;
    }

    // -------------------------------------------------------
    // QUERY PER SEGNALANTE
    // -------------------------------------------------------

    /**
     * Tutti gli interventi segnalati da un condomino specifico.
     * Usato nella sezione "le mie segnalazioni" del Condomino.
     */
    public function findBySegnalante(Condomino $condomino): array
    {
        return $this->getRepository()->findBy(
            ['segnalante' => $condomino],
            ['dataCreazione' => 'DESC']
        );
    }

    // -------------------------------------------------------
    // QUERY RECENTI (per la dashboard)
    // -------------------------------------------------------

    /**
     * Gli N interventi più recenti.
     * Usato nel widget "Lavori recenti" della dashboard Admin.
     */
    public function findRecenti(int $limite = 5): array
    {
        return $this->getRepository()->findBy(
            [],
            ['dataCreazione' => 'DESC'],
            $limite
        );
    }
}
