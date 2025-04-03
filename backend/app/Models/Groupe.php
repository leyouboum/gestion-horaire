<?php
declare(strict_types=1);

namespace app\Models;

class Groupe implements \JsonSerializable {
    private ?int $id_groupe;
    private string $nom_groupe;
    private int $nb_etudiants;

    public function __construct(?int $id_groupe, string $nom_groupe, int $nb_etudiants) {
        $this->id_groupe = $id_groupe;
        $this->nom_groupe = $nom_groupe;
        $this->nb_etudiants = $nb_etudiants;
    }

    public function getId(): ?int {
        return $this->id_groupe;
    }
    public function getNomGroupe(): string {
        return $this->nom_groupe;
    }
    public function getNbEtudiants(): int {
        return $this->nb_etudiants;
    }
    public function setNomGroupe(string $nom): void {
        $this->nom_groupe = $nom;
    }
    public function setNbEtudiants(int $nb): void {
        $this->nb_etudiants = $nb;
    }
    public function jsonSerialize(): array {
        return [
            'id_groupe'    => $this->id_groupe,
            'nom_groupe'   => $this->nom_groupe,
            'nb_etudiants' => $this->nb_etudiants,
        ];
    }
}
