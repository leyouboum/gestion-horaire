<?php
declare(strict_types=1);

namespace app\Models;

class Planning implements \JsonSerializable
{
    private ?int $id_planning;
    private int $id_salle;
    private int $id_cours;
    private int $id_groupe;
    private \DateTime $date_heure_debut;
    private \DateTime $date_heure_fin;
    private string $annee_academique;

    public function __construct(
        ?int $id_planning,
        int $id_salle,
        int $id_cours,
        int $id_groupe,
        \DateTime $date_heure_debut,
        \DateTime $date_heure_fin,
        string $annee_academique
    ) {
        $this->id_planning = $id_planning;
        $this->id_salle = $id_salle;
        $this->id_cours = $id_cours;
        $this->id_groupe = $id_groupe;
        $this->date_heure_debut = $date_heure_debut;
        $this->date_heure_fin = $date_heure_fin;
        $this->annee_academique = $annee_academique;
    }

    public function getId(): ?int {
        return $this->id_planning;
    }

    public function getIdSalle(): int {
        return $this->id_salle;
    }

    public function getIdCours(): int {
        return $this->id_cours;
    }

    public function getIdGroupe(): int {
        return $this->id_groupe;
    }

    public function getDateHeureDebut(): \DateTime {
        return $this->date_heure_debut;
    }

    public function getDateHeureFin(): \DateTime {
        return $this->date_heure_fin;
    }

    public function getAnneeAcademique(): string {
        return $this->annee_academique;
    }

    public function setIdSalle(int $idSalle): void {
        $this->id_salle = $idSalle;
    }

    public function setIdCours(int $idCours): void {
        $this->id_cours = $idCours;
    }

    public function setIdGroupe(int $idGroupe): void {
        $this->id_groupe = $idGroupe;
    }

    public function setDateHeureDebut(\DateTime $dateDebut): void {
        $this->date_heure_debut = $dateDebut;
    }

    public function setDateHeureFin(\DateTime $dateFin): void {
        $this->date_heure_fin = $dateFin;
    }

    public function setAnneeAcademique(string $annee): void {
        $this->annee_academique = $annee;
    }

    public function jsonSerialize(): array {
        return [
            'id_planning'      => $this->id_planning,
            'id_salle'         => $this->id_salle,
            'id_cours'         => $this->id_cours,
            'id_groupe'        => $this->id_groupe,
            'date_heure_debut' => $this->date_heure_debut->format('Y-m-d H:i:s'),
            'date_heure_fin'   => $this->date_heure_fin->format('Y-m-d H:i:s'),
            'annee_academique' => $this->annee_academique,
        ];
    }
}
