<?php
// src/Entity/InCorso.php
// Il fornitore ha avviato i lavori.
// priorita e fornitore sono ereditati da Stato.
//


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class InCorso extends Stato
{
    // Data/ora in cui il fornitore ha avviato i lavori
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dataAvvio = null;

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getDataAvvio(): ?\DateTimeInterface  { return $this->dataAvvio; }
    public function setDataAvvio(?\DateTimeInterface $v): void { $this->dataAvvio = $v; }

    public function getTipo(): string { return 'in_corso'; }
}
