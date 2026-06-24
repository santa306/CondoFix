<?php
// src/Foundation/FUtente.php
//
// Gestisce tutte le query relative agli Utenti.
// Contiene il metodo di autenticazione (login).

class FUtente extends FBase
{
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->entityClass = Utente::class;
    }

    // -------------------------------------------------------
    // AUTENTICAZIONE
    // -------------------------------------------------------

    /**
     * Cerca un utente per email e verifica la password.
     * Restituisce l'oggetto Utente se le credenziali sono valide,
     * null altrimenti.
     *
     * Usato dal Control di login (CLogin.php).
     *
     * Uso: $utente = $pm->utente()->login('mario@mail.it', 'password123');
     */
    public function login(string $email, string $password): ?Utente
    {
        $utente = $this->getRepository()->findOneBy(['email' => $email]);
        if ($utente === null) {
            return null; // email non trovata
        }
        if (!$utente->verificaPassword($password)) {
            return null; // password errata
        }
        return $utente;
    }

    /**
     * Cerca un utente tramite email.
     * Usato per verificare se una email è già registrata.
     */
    public function findByEmail(string $email): ?Utente
    {
        return $this->getRepository()->findOneBy(['email' => $email]);
    }

    /**
     * Controlla se esiste già un utente con questa email.
     * Usato nella registrazione per evitare duplicati.
     */
    public function emailEsistente(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    // -------------------------------------------------------
    // QUERY PER RUOLO
    // -------------------------------------------------------

    /**
     * Tutti gli amministratori registrati.
     */
    public function findAllAmministratori(): array
    {
        return $this->em->getRepository(Amministratore::class)->findAll();
    }

    /**
     * Tutti i fornitori registrati.
     * Usato nel form "Nuovo Lavoro" per popolare il select del fornitore.
     */
    public function findAllFornitori(): array
    {
        return $this->em->getRepository(Fornitore::class)->findAll();
    }

    /**
     * Tutti i fornitori di una categoria specifica.
     * Usato nel form di assegnazione per filtrare i fornitori per tipo.
     */
    public function findFornitoriByCategoria(Categoria $categoria): array
    {
        return $this->em->getRepository(Fornitore::class)->findBy(
            ['categoria' => $categoria]
        );
    }

    /**
     * Tutti i condomini di un condominio specifico.
     * Usato dall'Admin per gestire i residenti.
     */
    public function findCondominiByCondominio(Condominio $condominio): array
    {
        return $this->em->getRepository(Condomino::class)->findBy(
            ['condominio' => $condominio]
        );
    }

    /**
     * Tutti i fornitori (lavoratori) creati da un certo amministratore.
     * Usato per l'isolamento dei dati: ogni admin vede solo i propri.
     */
    public function findFornitoriByAmministratore(Amministratore $admin): array
    {
        return $this->em->getRepository(Fornitore::class)->findBy(
            ['amministratore' => $admin]
        );
    }
}
