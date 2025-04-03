<?php
declare(strict_types=1);

namespace app\Models;

class Site implements \JsonSerializable
{
    private ?int $id_site;
    private int $id_universite;
    private string $nom;
    private string $heure_ouverture;
    private string $heure_fermeture;

    public function __construct(
        ?int $id_site,
        int $id_universite,
        string $nom,
        string $heure_ouverture,
        string $heure_fermeture
    ) {
        $this->id_site = $id_site;
        $this->id_universite = $id_universite;
        $this->nom = $nom;
        $this->heure_ouverture = $heure_ouverture;
        $this->heure_fermeture = $heure_fermeture;
    }

    public function getId(): ?int {
        return $this->id_site;
    }

    public function getIdUniversite(): int {
        return $this->id_universite;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getHeureOuverture(): string {
        return $this->heure_ouverture;
    }

    public function getHeureFermeture(): string {
        return $this->heure_fermeture;
    }

    public function setIdUniversite(int $id_universite): void {
        $this->id_universite = $id_universite;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setHeureOuverture(string $heure): void {
        $this->heure_ouverture = $heure;
    }

    public function setHeureFermeture(string $heure): void {
        $this->heure_fermeture = $heure;
    }

    public function jsonSerialize(): array {
        return [
            'id_site'         => $this->id_site,
            'id_universite'   => $this->id_universite,
            'nom'             => $this->nom,
            'heure_ouverture' => $this->heure_ouverture,
            'heure_fermeture' => $this->heure_fermeture,
        ];
    }
}
