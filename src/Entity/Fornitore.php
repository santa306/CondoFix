<?php
// src/Entity/Fornitore.php
// Lavoratore/artigiano che esegue gli interventi.
// Può visualizzare e aggiornare solo i lavori assegnati a lui.

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Fornitore extends Utente
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $telefono = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $partitaIva = null;

    // Specializzazione del fornitore (Idraulico, Elettricista)
    //diventa la colonna categoria_id
    #[ORM\ManyToOne(targetEntity: Categoria::class)]
    private Categoria|null $categoria = null;

    // Amministratore che ha creato/gestisce questo lavoratore.
    // Serve all'isolamento dei dati: un admin vede e assegna solo i PROPRI
    // lavoratori. Nullable per compatibilità con eventuali fornitori già
    // esistenti senza admin associato.
    #[ORM\ManyToOne(targetEntity: Amministratore::class)]
    #[ORM\JoinColumn(nullable: true)]
    private Amministratore|null $amministratore = null;

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getAmministratore(): ?Amministratore { return $this->amministratore; }
    public function setAmministratore(?Amministratore $v): void { $this->amministratore = $v; }

    public function getTelefono(): ?string    { return $this->telefono; }
    public function setTelefono(?string $v): void { $this->telefono = $v; }

    public function getPartitaIva(): ?string  { return $this->partitaIva; }
    public function setPartitaIva(?string $v): void { $this->partitaIva = $v; }

    public function getCategoria(): ?Categoria  { return $this->categoria; }
    public function setCategoria(?Categoria $v): void { $this->categoria = $v; }

    public function getRuoloLabel(): string { return 'Lavoratore'; }
}
