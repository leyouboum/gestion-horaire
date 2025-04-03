<?php
declare(strict_types=1);

namespace app\Models;

class Materiel implements \JsonSerializable {
    private ?int $id_materiel;
    private string $type_materiel;
    private bool $is_mobile;
    private ?int $id_salle_fixe;
    // Nouveau champ pour le site d'affectation (optionnel)
    private ?int $id_site_affectation;

    public function __construct(?int $id_materiel, string $type_materiel, bool $is_mobile, ?int $id_salle_fixe = null, ?int $id_site_affectation = null) {
        $this->id_materiel = $id_materiel;
        $this->type_materiel = $type_materiel;
        $this->is_mobile = $is_mobile;
        $this->id_salle_fixe = $id_salle_fixe;
        $this->id_site_affectation = $id_site_affectation;
    }

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

    public function jsonSerialize(): array {
        return [
            'id_materiel'        => $this->id_materiel,
            'type_materiel'      => $this->type_materiel,
            'is_mobile'          => $this->is_mobile,
            'id_salle_fixe'      => $this->id_salle_fixe,
            'id_site_affectation'=> $this->id_site_affectation,
        ];
    }
}
