<?php
// src/Foundation/PersistentManager.php
//
// RUOLO: unica classe pubblica dello strato Foundation accessibile
//        dagli strati superiori (Control, View).
//        Fa da FACADE: delega tutto alle classi F-specifiche,
//        così i Control non conoscono mai Doctrine o le F-classi
//        direttamente.
//
// PATTERN: Singleton — una sola istanza per richiesta HTTP.
//
// STRUTTURA INTERNA:
//   PersistentManager
//     ├── FIntervento   → query su Interventi
//     ├── FUtente       → query su Utenti + login
//     ├── FCondominio   → query su Condomini
//     ├── FCategoria    → query su Categorie
//     ├── FNota         → query su Note
//     └── FFoto         → query su Foto
//
// COME SI USA (dal Control):
//
//   $pm = PersistentManager::getInstance();
//
//   // CRUD generico:
//   $pm->store($intervento);
//   $pm->delete($nota);
//   $pm->update();
//
//   // Query specifiche tramite delegazione:
//   $pm->intervento()->findByCondominio($cond);
//   $pm->utente()->login('mario@mail.it', 'pass');
//   $pm->condominio()->findByAmministratore($admin);

use Doctrine\ORM\EntityManagerInterface;

class PersistentManager
{
    // -------------------------------------------------------
    // Istanza Singleton
    // -------------------------------------------------------
    private static ?PersistentManager $instance = null;

    // EntityManager di Doctrine
    private EntityManagerInterface $em;

    // -------------------------------------------------------
    // Istanze delle classi Foundation specifiche
    // Vengono create una volta sola (lazy initialization)
    // -------------------------------------------------------
    private ?FIntervento $fIntervento  = null;
    private ?FUtente     $fUtente      = null;
    private ?FCondominio $fCondominio  = null;
    private ?FCategoria  $fCategoria   = null;
    private ?FNota       $fNota        = null;
    private ?FFoto       $fFoto        = null;

    // -------------------------------------------------------
    // COSTRUTTORE PRIVATO
    // -------------------------------------------------------
    private function __construct()
    {
        global $entityManager;
        $this->em = $entityManager;
    }

    // -------------------------------------------------------
    // getInstance() — punto di accesso globale
    // -------------------------------------------------------
    public static function getInstance(): PersistentManager
    {
        if (self::$instance === null) {
            require_once __DIR__ . '/../../bootstrap.php';
            self::$instance = new PersistentManager();
        }
        return self::$instance;
    }

    // -------------------------------------------------------
    // OPERAZIONI CRUD GENERICHE
    // Usate quando si ha già l'oggetto in mano e si vuole
    // solo salvarlo, aggiornarlo o eliminarlo.
    // -------------------------------------------------------

    /**
     * Salva un nuovo oggetto nel database (INSERT).
     * Funziona con qualsiasi Entity.
     *
     * Uso: $pm->store($intervento);
     */
    public function store(object $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * Aggiorna un oggetto esistente (UPDATE).
     * Modificare i campi con i setter, poi chiamare update().
     *
     * Uso: $intervento->setTitolo("Nuovo"); $pm->update();
     */
    public function update(): void
    {
        $this->em->flush();
    }

    /**
     * Elimina un oggetto dal database (DELETE).
     *
     * Uso: $pm->delete($nota);
     */
    public function delete(object $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * Carica un oggetto tramite classe e id.
     * Per query più specifiche usare i metodi delle F-classi.
     *
     * Uso: $i = $pm->load(Intervento::class, 42);
     */
    public function load(string $class, int $id): ?object
    {
        return $this->em->find($class, $id);
    }

    // -------------------------------------------------------
    // DELEGATORI ALLE CLASSI F-SPECIFICHE
    // Ogni metodo restituisce l'istanza della F-classe
    // corrispondente, creandola la prima volta (lazy).
    //
    // Il Control usa sempre la forma:
    //   $pm->intervento()->findByCondominio($cond)
    //   $pm->utente()->login($email, $pass)
    //   ecc.
    // -------------------------------------------------------

    /**
     * Accesso alle query sugli Interventi.
     *
     * Esempi:
     *   $pm->intervento()->findByCondominio($c)
     *   $pm->intervento()->findPresentati()
     *   $pm->intervento()->findByFornitore($f)
     *   $pm->intervento()->findGroupedByStato()
     *   $pm->intervento()->findRecenti(10)
     */
    public function intervento(): FIntervento
    {
        if ($this->fIntervento === null) {
            $this->fIntervento = new FIntervento($this->em);
        }
        return $this->fIntervento;
    }

    /**
     * Accesso alle query sugli Utenti e al login.
     *
     * Esempi:
     *   $pm->utente()->login($email, $password)
     *   $pm->utente()->findAllFornitori()
     *   $pm->utente()->findFornitoriByCategoria($cat)
     *   $pm->utente()->emailEsistente($email)
     */
    public function utente(): FUtente
    {
        if ($this->fUtente === null) {
            $this->fUtente = new FUtente($this->em);
        }
        return $this->fUtente;
    }

    /**
     * Accesso alle query sui Condomini.
     *
     * Esempi:
     *   $pm->condominio()->findByAmministratore($admin)
     *   $pm->condominio()->findByNome("Centrale")
     */
    public function condominio(): FCondominio
    {
        if ($this->fCondominio === null) {
            $this->fCondominio = new FCondominio($this->em);
        }
        return $this->fCondominio;
    }

    /**
     * Accesso alle query sulle Categorie.
     *
     * Esempi:
     *   $pm->categoria()->findAll()
     *   $pm->categoria()->esisteNome("Idraulico")
     */
    public function categoria(): FCategoria
    {
        if ($this->fCategoria === null) {
            $this->fCategoria = new FCategoria($this->em);
        }
        return $this->fCategoria;
    }

    /**
     * Accesso alle query sulle Note.
     *
     * Esempi:
     *   $pm->nota()->findByIntervento($intervento)
     *   $pm->nota()->findUltimaByIntervento($intervento)
     */
    public function nota(): FNota
    {
        if ($this->fNota === null) {
            $this->fNota = new FNota($this->em);
        }
        return $this->fNota;
    }

    /**
     * Accesso alle query sulle Foto.
     *
     * Esempi:
     *   $pm->foto()->findByIntervento($intervento)
     *   $pm->foto()->countByIntervento($intervento)
     */
    public function foto(): FFoto
    {
        if ($this->fFoto === null) {
            $this->fFoto = new FFoto($this->em);
        }
        return $this->fFoto;
    }
}
