<?php
// src/Entity/Intervento.php
// Rappresenta un intervento di manutenzione nel condominio.
// Collegato a: Condominio, Stato, Note (1-a-molti), Foto (1-a-molti),
//              Condomino segnalante.
//
// FIX rispetto al codice originale:
//   - $foto era ?string -> ora è una Collection di Foto (OneToMany)
//   - Aggiunta relazione con il Condomino che ha segnalato il problema
//   - Aggiunta collection di Note (OneToMany)
//   - Stato collegato con OneToOne con cascade persist/remove

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;//serve per foto e note
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'interventi')]
class Intervento
{
    // -------------------------------------------------------
    // ATTRIBUTI
    // -------------------------------------------------------

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $titolo;

    #[ORM\Column(type: 'text')]
    private string $descrizione;

    // Data di creazione della segnalazione (impostata automaticamente)
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dataCreazione;

    // -------------------------------------------------------
    // RELAZIONI
    // -------------------------------------------------------

    // Condominio in cui si svolge l'intervento
    #[ORM\ManyToOne(targetEntity: Condominio::class)]
    #[ORM\JoinColumn(nullable: false)]//un intervento deve avere un condominio (non ha senso un guasto senza edificio)
    private Condominio|null $condominio = null;//diventa fk condominio_id

    // Condomino che ha inviato la segnalazione (nullable: admin può creare interventi)
    #[ORM\ManyToOne(targetEntity: Condomino::class)]
    #[ORM\JoinColumn(nullable: true)]
    private Condomino|null $segnalante = null;

    // Stato corrente dell'intervento (OneToOne con cascade)
    // cascade: ['persist', 'remove'] -> quando salvo/elimino l'intervento,
    // salvo/elimino anche lo stato automaticamente
    #[ORM\OneToOne(targetEntity: Stato::class, cascade: ['persist', 'remove'])]//se volessimo che lo stato si cancella quando diventa orfano dovremmo mettere orphanRemoval: true
    #[ORM\JoinColumn(nullable: false)]
    private Stato|null $stato = null;

    // Note operative del fornitore (OneToMany)
    // mappedBy: specifica il campo in Nota che punta a Intervento
    // cascade: le note vengono eliminate con l'intervento
    // orphanRemoval: se una nota viene rimossa dalla collection, viene eliminata dal DB
    #[ORM\OneToMany(targetEntity: Nota::class, mappedBy: 'intervento',
                    cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $note;

    // Foto allegate (OneToMany)
    #[ORM\OneToMany(targetEntity: Foto::class, mappedBy: 'intervento',
                    cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $foto;

    // -------------------------------------------------------
    // COSTRUTTORE
    // -------------------------------------------------------

    public function __construct()
    {
        $this->dataCreazione = new \DateTime();
        $this->note = new ArrayCollection();
        $this->foto = new ArrayCollection();
    }

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getId(): ?int { return $this->id; }

    public function getTitolo(): string        { return $this->titolo; }
    public function setTitolo(string $v): void { $this->titolo = $v; }

    public function getDescrizione(): string        { return $this->descrizione; }
    public function setDescrizione(string $v): void { $this->descrizione = $v; }

    public function getDataCreazione(): \DateTimeInterface { return $this->dataCreazione; }

    public function getCondominio(): ?Condominio  { return $this->condominio; }
    public function setCondominio(?Condominio $v): void { $this->condominio = $v; }

    public function getSegnalante(): ?Condomino  { return $this->segnalante; }
    public function setSegnalante(?Condomino $v): void { $this->segnalante = $v; }

    public function getStato(): ?Stato  { return $this->stato; }
    public function setStato(?Stato $v): void { $this->stato = $v; }

    // --- Gestione Note ---

    public function getNote(): Collection { return $this->note; }

    public function addNota(Nota $nota): void
    {
        if (!$this->note->contains($nota)) {
            $this->note->add($nota);
            $nota->setIntervento($this);//definisce nota proprietario
        }
    }

    public function removeNota(Nota $nota): void
    {
        $this->note->removeElement($nota);
    }

    // --- Gestione Foto ---

    public function getFoto(): Collection { return $this->foto; }

    public function addFoto(Foto $foto): void
    {
        if (!$this->foto->contains($foto)) {
            $this->foto->add($foto);
            $foto->setIntervento($this);//fa si che anche foto sappia a che intervento appartenga
        }// foto proprietaria
    }

    public function removeFoto(Foto $foto): void
    {
        $this->foto->removeElement($foto);
    }
}
