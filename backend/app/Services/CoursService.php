<?php
declare(strict_types=1);

namespace app\Services;

use app\Models\Cours;
use app\Repositories\CoursRepository;

class CoursService {
    protected CoursRepository $coursRepository;

    public function __construct() {
        $this->coursRepository = new CoursRepository();
    }

    /**
     * Récupère l'ensemble des cours et les transforme en objets Cours.
     *
     * @return Cours[]
     */
    public function getAllCours(): array {
        $data = $this->coursRepository->getAllCours();
        $courses = [];
        foreach ($data as $row) {
            $sites = isset($row['sites']) && !empty($row['sites'])
                ? explode(', ', $row['sites'])
                : [];
            $courses[] = new Cours(
                (int)$row['id_cours'],
                $row['code_cours'],
                $row['nom_cours'],
                $row['details'] ?? null,
                (int)$row['duree'],
                $sites
            );
        }
        return $courses;
    }

    /**
     * Récupère un cours par son identifiant et le retourne sous forme d'objet Cours.
     *
     * @param int $id
     * @return Cours|null
     */
    public function getCoursById(int $id): ?Cours {
        $data = $this->coursRepository->getCoursById($id);
        if (!$data) {
            return null;
        }
        $sites = isset($data['sites']) && !empty($data['sites'])
            ? explode(', ', $data['sites'])
            : [];
        return new Cours(
            (int)$data['id_cours'],
            $data['code_cours'],
            $data['nom_cours'],
            $data['details'] ?? null,
            (int)$data['duree'],
            $sites
        );
    }

    /**
     * Crée un nouveau cours.
     *
     * @param array $data
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function createCours(array $data): bool {
        if (empty($data['code_cours']) || empty($data['nom_cours']) || !isset($data['duree'])) {
            throw new \InvalidArgumentException("Les informations du cours sont incomplètes.");
        }
        return $this->coursRepository->createCours($data);
    }

    /**
     * Met à jour un cours existant.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateCours(int $id, array $data): bool {
        return $this->coursRepository->updateCours($id, $data);
    }

    /**
     * Supprime un cours.
     *
     * @param int $id
     * @return bool
     */
    public function deleteCours(int $id): bool {
        return $this->coursRepository->deleteCours($id);
    }

    /**
     * Associe un cours à un groupe.
     *
     * @param int $coursId
     * @param int $groupId
     * @return bool
     */
    public function assignCoursToGroup(int $coursId, int $groupId): bool {
        return $this->coursRepository->assignCoursToGroup($coursId, $groupId);
    }

    /**
     * Retire l'association d'un cours à un groupe.
     *
     * @param int $coursId
     * @param int $groupId
     * @return bool
     */
    public function removeCoursFromGroup(int $coursId, int $groupId): bool {
        return $this->coursRepository->removeCoursFromGroup($coursId, $groupId);
    }
}
