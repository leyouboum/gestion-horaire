<?php
declare(strict_types=1);

namespace app\Models;

class Materiel implements \JsonSerializable {
    private ?int $id_materiel;
    private string $type_materiel;
    private bool $is_mobile;
    private ?int $id_salle_fixe;
    private ?int $id_site_affectation;

    // On stocke aussi le nom de la salle et du site (facultatif mais pratique pour l'affichage)
    private ?string $salle_fixe_name;
    private ?string $site_affectation_name;

    public function __construct(
        ?int $id_materiel,
        string $type_materiel,
        bool $is_mobile,
        ?int $id_salle_fixe = null,
        ?int $id_site_affectation = null,
        ?string $salle_fixe_name = null,
        ?string $site_affectation_name = null
    ) {
        $this->id_materiel = $id_materiel;
        $this->type_materiel = $type_materiel;
        $this->is_mobile = $is_mobile;
        $this->id_salle_fixe = $id_salle_fixe;
        $this->id_site_affectation = $id_site_affectation;
        $this->salle_fixe_name = $salle_fixe_name;
        $this->site_affectation_name = $site_affectation_name;
    }

    // --- Getters principaux ---
    public function getId(): ?int {
        return $this->id_materiel;
    }
    public function getTypeMateriel(): string {
        return $this->type_materiel;
    }
    public function getIsMobile(): bool {
        return $this->is_mobile;
    }
    public function getIdSalleFixe(): ?int {
        return $this->id_salle_fixe;
    }
    public function getIdSiteAffectation(): ?int {
        return $this->id_site_affectation;
    }

    // --- Getters pour les noms (si besoin) ---
    public function getSalleFixeName(): ?string {
        return $this->salle_fixe_name;
    }
    public function getSiteAffectationName(): ?string {
        return $this->site_affectation_name;
    }

    // --- Setters principaux ---
    public function setTypeMateriel(string $type): void {
        $this->type_materiel = $type;
    }
    public function setIsMobile(bool $isMobile): void {
        $this->is_mobile = $isMobile;
    }
    public function setIdSalleFixe(?int $idSalle): void {
        $this->id_salle_fixe = $idSalle;
    }
    public function setIdSiteAffectation(?int $idSite): void {
        $this->id_site_affectation = $idSite;
    }

    // --- Setters pour les noms ---
    public function setSalleFixeName(?string $name): void {
        $this->salle_fixe_name = $name;
    }
    public function setSiteAffectationName(?string $name): void {
        $this->site_affectation_name = $name;
    }

    /**
     * Pour que le JSON contienne l'ID + nom complet.
     */
    public function jsonSerialize(): array {
        return [
            'id_materiel'          => $this->id_materiel,
            'type_materiel'        => $this->type_materiel,
            'is_mobile'            => $this->is_mobile,
            'id_salle_fixe'        => $this->id_salle_fixe,
            'id_site_affectation'  => $this->id_site_affectation,
            // Noms en plus pour l'affichage direct
            'salle_fixe'           => $this->salle_fixe_name,
            'site_affectation'     => $this->site_affectation_name,
        ];
    }
}
