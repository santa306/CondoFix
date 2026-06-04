<?php
// src/Entity/Utente.php
// Classe BASE per tutti gli utenti del sistema (Admin, Condomino, Fornitore).
// Strategia SINGLE_TABLE: una sola tabella 'utenti' con una colonna 'ruolo'
// che discrimina il tipo di utente.

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'utenti')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'ruolo', type: 'string')]
#[ORM\DiscriminatorMap([
    'amministratore' => Amministratore::class,
    'condomino'      => Condomino::class,
    'fornitore'      => Fornitore::class,
])]
abstract class Utente
{
    // -------------------------------------------------------
    // ATTRIBUTI COMUNI A TUTTI GLI UTENTI
    // -------------------------------------------------------

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected int|null $id = null;

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

    // Verifica se la password fornita corrisponde all'hash salvato
    public function verificaPassword(string $passwordChiara): bool {
        return password_verify($passwordChiara, $this->password);
    }
}
