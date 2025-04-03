<?php
declare(strict_types=1);

namespace app\Controllers\Admin;

use app\Repositories\UniversiteRepository;

class UniversiteController {
    private UniversiteRepository $universiteRepo;

    public function __construct() {
        $this->universiteRepo = new UniversiteRepository();
    }

    /**
     * Liste toutes les universités.
     *
     * @return array
     */
    public function listUniversites(): array {
        return $this->universiteRepo->getAll();
    }

    /**
     * Retourne une université par son ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getUniversite(int $id): ?array {
        return $this->universiteRepo->getById($id);
    }

    /**
     * Crée une nouvelle université.
     * Renvoie false en cas de doublon.
     *
     * @param array $data
     * @return bool
     */
    public function createUniversite(array $data): bool {
        return $this->universiteRepo->create($data);
    }

    /**
     * Met à jour une université existante.
     * Renvoie false en cas de doublon.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateUniversite(int $id, array $data): bool {
        return $this->universiteRepo->update($id, $data);
    }

    /**
     * Supprime une université.
     *
     * @param int $id
     * @return bool
     */
    public function deleteUniversite(int $id): bool {
        return $this->universiteRepo->delete($id);
    }
}
