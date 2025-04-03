<?php
declare(strict_types=1);

namespace app\Controllers\Admin;

use app\Repositories\SalleRepository;

class SalleController {
    private SalleRepository $salleRepo;

    public function __construct() {
        $this->salleRepo = new SalleRepository();
    }

    /**
     * Retourne la liste de toutes les salles.
     *
     * @return array
     */
    public function listSalles(): array {
        return $this->salleRepo->getAllSalles();
    }

    /**
     * Retourne une salle par son ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getSalle(int $id): ?array {
        return $this->salleRepo->getSalleById($id);
    }

    /**
     * Crée une nouvelle salle.
     *
     * @param array $data
     * @return bool
     */
    public function createSalle(array $data): bool {
        return $this->salleRepo->createSalle($data);
    }

    /**
     * Met à jour une salle existante.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateSalle(int $id, array $data): bool {
        return $this->salleRepo->updateSalle($id, $data);
    }

    /**
     * Supprime une salle.
     *
     * @param int $id
     * @return bool
     */
    public function deleteSalle(int $id): bool {
        return $this->salleRepo->deleteSalle($id);
    }

    /**
     * Retourne les salles associées à un site.
     *
     * @param int $idSite
     * @return array
     */
    public function listSallesBySite(int $idSite): array {
        return $this->salleRepo->getBySite($idSite);
    }

    /**
     * Retourne les salles associées à un groupe.
     *
     * @param int $groupId
     * @return array
     */
    public function listSallesByGroup(int $groupId): array {
        return $this->salleRepo->getSallesByGroup($groupId);
    }
}
