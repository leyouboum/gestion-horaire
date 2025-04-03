<?php
declare(strict_types=1);

namespace app\Models;

class Salle implements \JsonSerializable
{
    private ?int $id_salle;
    private int $id_site;
    private string $nom_salle;
    private int $capacite_max;

    public function __construct(?int $id_salle, int $id_site, string $nom_salle, int $capacite_max)
    {
        $this->id_salle = $id_salle;
        $this->id_site = $id_site;
        $this->nom_salle = $nom_salle;
        $this->capacite_max = $capacite_max;
    }

    public function getId(): ?int {
        return $this->id_salle;
    }

    public function getIdSite(): int {
        return $this->id_site;
    }

    public function getNomSalle(): string {
        return $this->nom_salle;
    }

    public function getCapaciteMax(): int {
        return $this->capacite_max;
    }

    public function setIdSite(int $id_site): void {
        $this->id_site = $id_site;
    }

    public function setNomSalle(string $nom): void {
        $this->nom_salle = $nom;
    }

    public function setCapaciteMax(int $capacite): void {
        $this->capacite_max = $capacite;
    }

    public function jsonSerialize(): array {
        return [
            'id_salle'     => $this->id_salle,
            'id_site'      => $this->id_site,
            'nom_salle'    => $this->nom_salle,
            'capacite_max' => $this->capacite_max,
        ];
    }
}
