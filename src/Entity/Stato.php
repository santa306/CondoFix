<?php
// src/Entity/Stato.php
// Classe ASTRATTA base per tutti gli stati di un Intervento.
// Strategia SINGLE_TABLE: una sola tabella 'stati' con colonna 'tipo'
// che discrimina lo stato concreto.
//
// GERARCHIA CORRETTA:
//   Stato (abstract)
//     ├── Presentato   → segnalazione appena inviata dal condomino
//     ├── Negato       → admin ha rifiutato la segnalazione
//     ├── Accettato    → admin ha approvato e assegnato al fornitore
//     ├── InCorso      → fornitore ha avviato i lavori
//     └── Completato   → fornitore ha terminato; admin può allegare fattura
//


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'stati')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'tipo', type: 'string')]
#[ORM\DiscriminatorMap([
    'presentato' => Presentato::class,
    'negato'     => Negato::class,
    'accettato'  => Accettato::class,
    'in_corso'   => InCorso::class,
    'completato' => Completato::class,
])]
abstract class Stato
{
    // -------------------------------------------------------
    // ATTRIBUTI
    // -------------------------------------------------------

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected int|null $id = null;

    // Priorità: presente in Accettato, InCorso e Completato (nullable per gli altri)
    // Valori attesi: 'alta', 'media', 'bassa'
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $priorita = null;

    // Fornitore assegnato: presente in Accettato, InCorso e Completato
    #[ORM\ManyToOne(targetEntity: Fornitore::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected Fornitore|null $fornitore = null;

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getId(): ?int { return $this->id; }

    public function getPriorita(): ?string    { return $this->priorita; }
    public function setPriorita(?string $v): void { $this->priorita = $v; }

    public function getFornitore(): ?Fornitore  { return $this->fornitore; }
    public function setFornitore(?Fornitore $v): void { $this->fornitore = $v; }

    // Metodo utile per ottenere il nome del tipo come stringa (usato nei template)
    abstract public function getTipo(): string;
}
//esiste Perché la colonna tipo la gestisce Doctrine internamente per il mapping, ma nel codice e nei template ti serve un modo per chiedere all'oggetto "che stato sei?"