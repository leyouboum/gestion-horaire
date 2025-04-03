<?php
declare(strict_types=1);

namespace app\Services;

use app\Models\Planning;
use app\Repositories\PlanningRepository;

class PlanningService {
    protected PlanningRepository $planningRepository;

    public function __construct() {
        $this->planningRepository = new PlanningRepository();
    }

    /**
     * Récupère tous les plannings et les transforme en objets Planning.
     *
     * @return Planning[]
     */
    public function getAllPlannings(): array {
        $data = $this->planningRepository->getAll();
        $plannings = [];
        foreach ($data as $row) {
            $plannings[] = new Planning(
                isset($row['id_planning']) ? (int)$row['id_planning'] : null,
                (int)$row['id_salle'],
                (int)$row['id_cours'],
                (int)$row['id_groupe'],
                new \DateTime($row['date_heure_debut']),
                new \DateTime($row['date_heure_fin']),
                $row['annee_academique']
            );
        }
        return $plannings;
    }

    /**
     * Récupère le planning d'un groupe filtré par période.
     * Ici, on retourne directement le résultat du repository, car le résultat
     * inclut des jointures supplémentaires (noms de salle, site, cours, groupe, matériels, etc.).
     *
     * @param int $groupId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getPlanningByGroup(int $groupId, ?string $startDate = null, ?string $endDate = null): array {
        return $this->planningRepository->getPlanningByGroup($groupId, $startDate, $endDate);
    }

    /**
     * Récupère un planning par son identifiant et le transforme en objet Planning.
     *
     * @param int $id
     * @return Planning|null
     */
    public function getPlanningById(int $id): ?Planning {
        $data = $this->planningRepository->getPlanningById($id);
        if (!$data) {
            return null;
        }
        return new Planning(
            (int)$data['id_planning'],
            (int)$data['id_salle'],
            (int)$data['id_cours'],
            (int)$data['id_groupe'],
            new \DateTime($data['date_heure_debut']),
            new \DateTime($data['date_heure_fin']),
            $data['annee_academique']
        );
    }

    /**
     * Crée un nouveau planning.
     *
     * @param array $data
     * @return bool
     */
    public function createPlanning(array $data): bool {
        return $this->planningRepository->createPlanning($data);
    }

    /**
     * Met à jour un planning existant.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updatePlanning(int $id, array $data): bool {
        return $this->planningRepository->updatePlanning($id, $data);
    }

    /**
     * Supprime un planning.
     *
     * @param int $id
     * @return bool
     */
    public function deletePlanning(int $id): bool {
        return $this->planningRepository->deletePlanning($id);
    }
}
