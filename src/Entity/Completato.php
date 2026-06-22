<?php
// src/Entity/Completato.php
// Il fornitore ha terminato i lavori.
// L'amministratore può ora allegare la fattura.
// priorita e fornitore sono ereditati da Stato.

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Completato extends Stato
{
    // Data/ora in cui il fornitore ha dichiarato i lavori conclusi
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dataCompletamento = null;

    // Percorso del file fattura allegato dall'amministratore
    // Es: "uploads/fatture/fattura_123.pdf"
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $fattura = null;

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getDataCompletamento(): ?\DateTimeInterface  { return $this->dataCompletamento; }
    public function setDataCompletamento(?\DateTimeInterface $v): void { $this->dataCompletamento = $v; }

    public function getFattura(): ?string    { return $this->fattura; }
    public function setFattura(?string $v): void { $this->fattura = $v; }

    public function getTipo(): string { return 'completato'; }
}
