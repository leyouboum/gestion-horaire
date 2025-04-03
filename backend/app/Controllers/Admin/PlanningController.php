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
     * Retourne toutes les séances planifiées.
     *
     * @return array
     */
    public function listAllPlannings(): array {
        return $this->planningRepo->getAll();
    }

    /**
     * Retourne les créneaux planifiés d'un groupe, éventuellement filtrés par période.
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
     * Retourne une séance planifiée par son ID.
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
     * @param array $data
     * @return bool
     */
    public function createPlanning(array $data): bool {
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
