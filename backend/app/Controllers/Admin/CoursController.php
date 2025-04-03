<?php
declare(strict_types=1);

namespace app\Controllers\Admin;

use app\Repositories\CoursRepository;

class CoursController {
    private CoursRepository $coursRepo;

    public function __construct() {
        $this->coursRepo = new CoursRepository();
    }

    /**
     * Retourne tous les cours.
     *
     * @return array
     */
    public function listCours(): array {
        return $this->coursRepo->getAllCours();
    }

    /**
     * Retourne un cours par son ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getCours(int $id): ?array {
        return $this->coursRepo->getCoursById($id);
    }

    /**
     * Crée un nouveau cours.
     *
     * @param array $data
     * @return bool
     */
    public function createCours(array $data): bool {
        return $this->coursRepo->createCours($data);
    }

    /**
     * Met à jour un cours existant.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateCours(int $id, array $data): bool {
        return $this->coursRepo->updateCours($id, $data);
    }

    /**
     * Supprime un cours.
     *
     * @param int $id
     * @return bool
     */
    public function deleteCours(int $id): bool {
        return $this->coursRepo->deleteCours($id);
    }

    /**
     * Retourne les cours associés à un site.
     *
     * @param int $idSite
     * @return array
     */
    public function listCoursBySite(int $idSite): array {
        return $this->coursRepo->getBySite($idSite);
    }

    /**
     * Associe un cours à un groupe.
     *
     * @param int $coursId
     * @param int $groupId
     * @return bool
     */
    public function assignCoursToGroup(int $coursId, int $groupId): bool {
        return $this->coursRepo->assignCoursToGroup($coursId, $groupId);
    }

    /**
     * Retire l'association d'un cours à un groupe.
     *
     * @param int $coursId
     * @param int $groupId
     * @return bool
     */
    public function removeCoursFromGroup(int $coursId, int $groupId): bool {
        return $this->coursRepo->removeCoursFromGroup($coursId, $groupId);
    }
}
