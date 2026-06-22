<?php
// src/Entity/Utente.php
// Classe BASE per tutti gli utenti del sistema (Admin, Condomino, Fornitore).
// Strategia SINGLE_TABLE: una sola tabella 'utenti' con una colonna 'ruolo'
// che discrimina il tipo di utente.

use Doctrine\ORM\Mapping as ORM;// permette sotto chiamarlo ora solo ORM invece di [Doctrine\ORM\Mapping\Entity] 

#[ORM\Entity]
#[ORM\Table(name: 'utenti')]//la tabella si chiama utenti senza si chiamerebbe Utente come il nome del file
#[ORM\InheritanceType('SINGLE_TABLE')]//la nostra strategia tutte le classi non hanno una tabella per ognuna
#[ORM\DiscriminatorColumn(name: 'ruolo', type: 'string')]//è la colonna discriminante che ti permette di definire un personaggio o l'altro
#[ORM\DiscriminatorMap([//se leggi amministratore istanzi una classe amministratore e cosi via
    'amministratore' => Amministratore::class,
    'condomino'      => Condomino::class,
    'fornitore'      => Fornitore::class,
])]
abstract class Utente
{
    // -------------------------------------------------------
    // ATTRIBUTI COMUNI A TUTTI GLI UTENTI
    // -------------------------------------------------------

    #[ORM\Id]//chiave primaria
    #[ORM\Column(type: 'integer')]//riferito sempre a id, dice che è un intero
    #[ORM\GeneratedValue]
    protected int|null $id = null;//un utente appena creato in memoria non ha ancora id (null), lo ottiene solo dopo essere stato salvato.

    #[ORM\Column(type: 'string')]
    protected string $nome;

    #[ORM\Column(type: 'string')]
    protected string $cognome;

    // Email univoca: usata come username per il login
    #[ORM\Column(type: 'string', unique: true)]
    protected string $email;

    // Password salvata come hash bcrypt (MAI in chiaro!)
    // Usare password_hash($password, PASSWORD_BCRYPT) prima di salvare
    #[ORM\Column(type: 'string')]
    protected string $password;

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getId(): ?int         { return $this->id; }

    public function getNome(): string     { return $this->nome; }
    public function setNome(string $v): void { $this->nome = $v; }

    public function getCognome(): string  { return $this->cognome; }
    public function setCognome(string $v): void { $this->cognome = $v; }

    public function getEmail(): string    { return $this->email; }
    public function setEmail(string $v): void { $this->email = $v; }

    public function getPassword(): string { return $this->password; }

    // Imposta la password eseguendo automaticamente l'hash bcrypt
    public function setPassword(string $passwordChiara): void {
        $this->password = password_hash($passwordChiara, PASSWORD_BCRYPT);
    }
//non salva la password così com'è: prima la passa in password_hash() con algoritmo bcrypt, poi salva l'hash
    
// Verifica se la password fornita corrisponde all'hash salvato
    public function verificaPassword(string $passwordChiara): bool {//restituirà un true false
        return password_verify($passwordChiara, $this->password);//senza mai decriptare
    }
}
