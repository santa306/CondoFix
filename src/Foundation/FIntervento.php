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

    // =======================================================
    // QUERY DI RICERCA PER TITOLO
    // Usate dalla barra di ricerca nelle dashboard. Ogni ruolo cerca
    // solo nel proprio insieme di lavori (stesso filtro delle query
    // normali) con in piu' il vincolo sul titolo (LIKE, case-insensitive).
    // =======================================================

    /**
     * Lavori ATTIVI di un fornitore il cui titolo contiene $cerca.
     * Variante "con ricerca" di findAttiviByFornitore().
     */
    public function cercaAttiviByFornitore(Fornitore $fornitore, string $cerca): array
    {
        return $this->em->createQuery('
            SELECT i FROM Intervento i JOIN i.stato s
            WHERE s.fornitore = :fornitore
            AND (s INSTANCE OF ' . Accettato::class . '
                 OR s INSTANCE OF ' . InCorso::class . ')
            AND LOWER(i.titolo) LIKE :cerca
            ORDER BY i.dataCreazione DESC
        ')
        ->setParameter('fornitore', $fornitore)
        ->setParameter('cerca', '%' . mb_strtolower($cerca) . '%')
        ->getResult();
    }

    /**
     * Segnalazioni di un condomino il cui titolo contiene $cerca.
     * Variante "con ricerca" di findBySegnalante().
     */
    public function cercaBySegnalante(Condomino $condomino, string $cerca): array
    {
        return $this->em->createQuery('
            SELECT i FROM Intervento i
            WHERE i.segnalante = :segnalante
            AND LOWER(i.titolo) LIKE :cerca
            ORDER BY i.dataCreazione DESC
        ')
        ->setParameter('segnalante', $condomino)
        ->setParameter('cerca', '%' . mb_strtolower($cerca) . '%')
        ->getResult();
    }

    /**
     * Tutti gli interventi il cui titolo contiene $cerca (per l'admin,
     * che vede tutto il sistema). Variante "con ricerca" di findRecenti().
     */
    public function cercaTutti(string $cerca): array
    {
        return $this->em->createQuery('
            SELECT i FROM Intervento i
            WHERE LOWER(i.titolo) LIKE :cerca
            ORDER BY i.dataCreazione DESC
        ')
        ->setParameter('cerca', '%' . mb_strtolower($cerca) . '%')
        ->getResult();
    }

    /**
     * Interventi di un condominio il cui titolo contiene $cerca.
     * Variante "con ricerca" di findByCondominio(): usata dalla barra di
     * ricerca nella dashboard del Condomino, che ora vede tutti i lavori
     * del proprio condominio (non solo le proprie segnalazioni).
     */
    public function cercaByCondominio(Condominio $condominio, string $cerca): array
    {
        return $this->em->createQuery('
            SELECT i FROM Intervento i
            WHERE i.condominio = :condominio
            AND LOWER(i.titolo) LIKE :cerca
            ORDER BY i.dataCreazione DESC
        ')
        ->setParameter('condominio', $condominio)
        ->setParameter('cerca', '%' . mb_strtolower($cerca) . '%')
        ->getResult();
    }


}
