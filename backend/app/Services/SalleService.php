<?php
declare(strict_types=1);

namespace app\Services;

use app\Models\Salle;
use app\Repositories\SalleRepository;

class SalleService {
    protected SalleRepository $salleRepository;

    public function __construct() {
        $this->salleRepository = new SalleRepository();
    }

    /**
     * Récupère toutes les salles et les transforme en objets Salle.
     *
     * @return Salle[]
     */
    public function getAllSalles(): array {
        $data = $this->salleRepository->getAllSalles();
        $salles = [];
        foreach ($data as $row) {
            $salles[] = new Salle(
                isset($row['id_salle']) ? (int)$row['id_salle'] : null,
                (int)$row['id_site'],
                $row['nom_salle'],
                (int)$row['capacite_max']
            );
        }
        return $salles;
    }

    /**
     * Récupère une salle par son ID et la retourne sous forme d'objet Salle.
     *
     * @param int $id
     * @return Salle|null
     */
    public function getSalleById(int $id): ?Salle {
        $data = $this->salleRepository->getSalleById($id);
        if (!$data) {
            return null;
        }
        return new Salle(
            isset($data['id_salle']) ? (int)$data['id_salle'] : null,
            (int)$data['id_site'],
            $data['nom_salle'],
            (int)$data['capacite_max']
        );
    }

    /**
     * Crée une nouvelle salle.
     *
     * @param array $data
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function createSalle(array $data): bool {
        if (empty($data['id_site']) || empty($data['nom_salle']) || empty($data['capacite_max'])) {
            throw new \InvalidArgumentException("Les informations de la salle sont incomplètes.");
        }
        return $this->salleRepository->createSalle($data);
    }

    /**
     * Met à jour une salle existante.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateSalle(int $id, array $data): bool {
        return $this->salleRepository->updateSalle($id, $data);
    }

    /**
     * Supprime une salle.
     *
     * @param int $id
     * @return bool
     */
    public function deleteSalle(int $id): bool {
        return $this->salleRepository->deleteSalle($id);
    }

    /**
     * Récupère les salles associées à un site donné.
     *
     * @param int $idSite
     * @return Salle[]
     */
    public function getBySite(int $idSite): array {
        $data = $this->salleRepository->getBySite($idSite);
        $salles = [];
        foreach ($data as $row) {
            $salles[] = new Salle(
                isset($row['id_salle']) ? (int)$row['id_salle'] : null,
                (int)$row['id_site'],
                $row['nom_salle'],
                (int)$row['capacite_max']
            );
        }
        return $salles;
    }

    /**
     * Récupère les salles associées à un groupe.
     *
     * @param int $groupId
     * @return Salle[]
     */
    public function getSallesByGroup(int $groupId): array {
        $data = $this->salleRepository->getSallesByGroup($groupId);
        $salles = [];
        foreach ($data as $row) {
            $salles[] = new Salle(
                isset($row['id_salle']) ? (int)$row['id_salle'] : null,
                (int)$row['id_site'],
                $row['nom_salle'],
                (int)$row['capacite_max']
            );
        }
        return $salles;
    }
}
