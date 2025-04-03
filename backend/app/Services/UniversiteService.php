<?php
declare(strict_types=1);

namespace app\Services;

use app\Models\Universite;
use app\Repositories\UniversiteRepository;

class UniversiteService {
    protected UniversiteRepository $universiteRepository;

    public function __construct() {
        $this->universiteRepository = new UniversiteRepository();
    }

    /**
     * Récupère toutes les universités et les transforme en objets Universite.
     *
     * @return Universite[]
     */
    public function getAllUniversites(): array {
        $data = $this->universiteRepository->getAll();
        $universites = [];
        foreach ($data as $row) {
            $universites[] = new Universite(
                (int)$row['id_universite'],
                $row['nom']
            );
        }
        return $universites;
    }

    /**
     * Récupère une université par son ID et la retourne sous forme d'objet Universite.
     *
     * @param int $id
     * @return Universite|null
     */
    public function getUniversiteById(int $id): ?Universite {
        $data = $this->universiteRepository->getById($id);
        if (!$data) {
            return null;
        }
        return new Universite(
            (int)$data['id_universite'],
            $data['nom']
        );
    }

    /**
     * Crée une nouvelle université.
     *
     * @param array $data
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function createUniversite(array $data): bool {
        if (empty($data['nom'])) {
            throw new \InvalidArgumentException("Le nom de l'université est requis.");
        }
        return $this->universiteRepository->create($data);
    }

    /**
     * Met à jour une université existante.
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function updateUniversite(int $id, array $data): bool {
        if (empty($data['nom'])) {
            throw new \InvalidArgumentException("Le nom de l'université est requis.");
        }
        return $this->universiteRepository->update($id, $data);
    }

    /**
     * Supprime une université.
     *
     * @param int $id
     * @return bool
     */
    public function deleteUniversite(int $id): bool {
        return $this->universiteRepository->delete($id);
    }
}
