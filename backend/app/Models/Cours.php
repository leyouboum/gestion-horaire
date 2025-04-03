<?php
declare(strict_types=1);

namespace app\Models;

class Cours implements \JsonSerializable {
    private ?int $id_cours;
    private string $code_cours;
    private string $nom_cours;
    private ?string $details;
    private int $duree;
    private ?array $sites;

    /**
     * Cours constructor.
     *
     * @param int|null $id_cours
     * @param string $code_cours
     * @param string $nom_cours
     * @param string|null $details
     * @param int $duree
     * @param array|null $sites
     */
    public function __construct(?int $id_cours, string $code_cours, string $nom_cours, ?string $details, int $duree, ?array $sites = null) {
        $this->id_cours    = $id_cours;
        $this->code_cours  = $code_cours;
        $this->nom_cours   = $nom_cours;
        $this->details     = $details;
        $this->duree       = $duree;
        $this->sites       = $sites;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id_cours;
    }

    /**
     * @return string
     */
    public function getCodeCours(): string {
        return $this->code_cours;
    }

    /**
     * @return string
     */
    public function getNomCours(): string {
        return $this->nom_cours;
    }

    /**
     * @return string|null
     */
    public function getDetails(): ?string {
        return $this->details;
    }

    /**
     * @return int
     */
    public function getDuree(): int {
        return $this->duree;
    }

    /**
     * @return array|null
     */
    public function getSites(): ?array {
        return $this->sites;
    }

    /**
     * @param string $code
     */
    public function setCodeCours(string $code): void {
        $this->code_cours = $code;
    }

    /**
     * @param string $nom
     */
    public function setNomCours(string $nom): void {
        $this->nom_cours = $nom;
    }

    /**
     * @param string|null $details
     */
    public function setDetails(?string $details): void {
        $this->details = $details;
    }

    /**
     * @param int $duree
     */
    public function setDuree(int $duree): void {
        $this->duree = $duree;
    }

    /**
     * @param array|null $sites
     */
    public function setSites(?array $sites): void {
        $this->sites = $sites;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize(): array {
        return [
            'id_cours'   => $this->id_cours,
            'code_cours' => $this->code_cours,
            'nom_cours'  => $this->nom_cours,
            'details'    => $this->details,
            'duree'      => $this->duree,
            'sites'      => $this->sites,
        ];
    }
}
