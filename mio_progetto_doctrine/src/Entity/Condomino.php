<?php
// src/Entity/Condomino.php
// Residente del condominio: può inviare segnalazioni e monitorare i lavori.

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Condomino extends Utente
{
    // Ogni condomino appartiene a UN condominio
    // (molti condomini -> 1 condominio)
    #[ORM\ManyToOne(targetEntity: Condominio::class)]
    #[ORM\JoinColumn(nullable: true)]
    private Condominio|null $condominio = null;

    // Numero interno/appartamento (es. "Scala A, Int. 5")
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $interno = null;

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getCondominio(): ?Condominio  { return $this->condominio; }
    public function setCondominio(?Condominio $v): void { $this->condominio = $v; }

    public function getInterno(): ?string  { return $this->interno; }
    public function setInterno(?string $v): void { $this->interno = $v; }
}
