<?php
// src/Entity/Foto.php
// Fotografia allegata a un Intervento (dal condomino o dal fornitore).
// Separata da Intervento perché un intervento può avere più foto.

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'foto')]
class Foto
{
    // -------------------------------------------------------
    // ATTRIBUTI
    // -------------------------------------------------------

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    // Percorso relativo del file salvato sul server
    // Es: "uploads/foto/intervento_42_1.jpg"
    #[ORM\Column(type: 'string')]
    private string $percorso;

    // Nome originale del file caricato dall'utente (per il download)
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $nomeOriginale = null;

    // Timestamp di caricamento (impostato automaticamente)
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $timestamp;

    // Ogni foto appartiene a 1 Intervento
    #[ORM\ManyToOne(targetEntity: Intervento::class, inversedBy: 'foto')]
    #[ORM\JoinColumn(nullable: false)]
    private Intervento|null $intervento = null;

    // -------------------------------------------------------
    // COSTRUTTORE
    // -------------------------------------------------------

    public function __construct()
    {
        $this->timestamp = new \DateTime();
    }

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getId(): ?int { return $this->id; }

    public function getPercorso(): string        { return $this->percorso; }
    public function setPercorso(string $v): void { $this->percorso = $v; }

    public function getNomeOriginale(): ?string    { return $this->nomeOriginale; }
    public function setNomeOriginale(?string $v): void { $this->nomeOriginale = $v; }

    public function getTimestamp(): \DateTimeInterface { return $this->timestamp; }

    public function getIntervento(): ?Intervento  { return $this->intervento; }
    public function setIntervento(?Intervento $v): void { $this->intervento = $v; }
}
