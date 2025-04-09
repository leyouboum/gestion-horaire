<?php
declare(strict_types=1);

namespace app\Models;

class AnneeAcademique implements \JsonSerializable
{
    private ?int $id_annee;
    private string $libelle;
    private \DateTime $date_debut;
    private \DateTime $date_fin;

    public function __construct(?int $id_annee, string $libelle, \DateTime $date_debut, \DateTime $date_fin)
    {
        $this->id_annee = $id_annee;
        $this->libelle = $libelle;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
    }

    public function getIdAnnee(): ?int
    {
        return $this->id_annee;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function getDateDebut(): \DateTime
    {
        return $this->date_debut;
    }

    public function getDateFin(): \DateTime
    {
        return $this->date_fin;
    }

    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    public function setDateDebut(\DateTime $date_debut): void
    {
        $this->date_debut = $date_debut;
    }

    public function setDateFin(\DateTime $date_fin): void
    {
        $this->date_fin = $date_fin;
    }

    public function jsonSerialize(): array
    {
        return [
            'id_annee'   => $this->id_annee,
            'libelle'    => $this->libelle,
            'date_debut' => $this->date_debut->format('Y-m-d'),
            'date_fin'   => $this->date_fin->format('Y-m-d'),
        ];
    }
}
