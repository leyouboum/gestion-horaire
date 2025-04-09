<?php
declare(strict_types=1);

namespace app\Controllers\Admin;

use app\Repositories\PlanningRepository;

class PlanningController {
    private PlanningRepository $planningRepo;

    public function __construct() {
        $this->planningRepo = new PlanningRepository();
    }

    /**
     * Retourne l'ensemble des séances planifiées.
     *
     * @return array
     */
    public function listAllPlannings(): array {
        return $this->planningRepo->getAll();
    }

    /**
     * Retourne les créneaux planifiés pour un groupe dans une période donnée.
     *
     * @param int $groupId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function listPlanningsByGroup(int $groupId, ?string $startDate = null, ?string $endDate = null): array {
        return $this->planningRepo->getPlanningByGroup($groupId, $startDate, $endDate);
    }

    /**
     * Retourne les créneaux planifiés pour un groupe filtrés par une année académique précise.
     *
     * @param int $groupId
     * @param int $anneeId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getPlanningByGroupAndAnnee(int $groupId, int $anneeId, ?string $startDate = null, ?string $endDate = null): array {
        return $this->planningRepo->getPlanningByGroupAndAnnee($groupId, $anneeId, $startDate, $endDate);
    }

    /**
     * Retourne une séance planifiée par son identifiant.
     *
     * @param int $id
     * @return array|null
     */
    public function getPlanning(int $id): ?array {
        return $this->planningRepo->getPlanningById($id);
    }

    /**
     * Crée une nouvelle séance planifiée.
     *
     * Expects $data incluant :
     * - 'id_salle', 'id_cours', 'id_groupe', 'date_heure_debut', 'date_heure_fin', 'id_annee'
     * - Optionnellement : 'statut' (défaut 'planifie') et 'commentaire'
     *
     * @param array $data
     * @return bool
     */
    public function createPlanning(array $data): bool {
        if (!isset($data['statut'])) {
            $data['statut'] = 'planifie';
        }
        return $this->planningRepo->createPlanning($data);
    }

    /**
     * Met à jour une séance planifiée existante.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updatePlanning(int $id, array $data): bool {
        return $this->planningRepo->updatePlanning($id, $data);
    }

    /**
     * Supprime une séance planifiée.
     *
     * @param int $id
     * @return bool
     */
    public function deletePlanning(int $id): bool {
        return $this->planningRepo->deletePlanning($id);
    }
}
