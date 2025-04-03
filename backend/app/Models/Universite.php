<?php
declare(strict_types=1);

namespace app\Models;

class Universite implements \JsonSerializable
{
    private ?int $id_universite;
    private string $nom;

    public function __construct(?int $id_universite, string $nom)
    {
        $this->id_universite = $id_universite;
        $this->nom = $nom;
    }

    public function getIdUniversite(): ?int
    {
        return $this->id_universite;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function jsonSerialize(): array
    {
        return [
            'id_universite' => $this->id_universite,
            'nom' => $this->nom,
        ];
    }
}
