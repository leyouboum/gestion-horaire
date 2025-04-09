<?php
declare(strict_types=1);

namespace app\Controllers\Admin;

use app\Repositories\AnneeAcademiqueRepository;

class AnneeAcademiqueController {
    private AnneeAcademiqueRepository $anneeRepo;

    public function __construct() {
        $this->anneeRepo = new AnneeAcademiqueRepository();
    }

    /**
     * Liste toutes les années académiques.
     *
     * @return array
     */
    public function listAnnees(): array {
        return $this->anneeRepo->getAll();
    }

    /**
     * Retourne une année académique par son identifiant.
     *
     * @param int $id
     * @return array|null
     */
    public function getAnnee(int $id): ?array {
        return $this->anneeRepo->getById($id);
    }

    /**
     * Crée une nouvelle année académique.
     *
     * @param array $data
     * @return bool
     */
    public function createAnnee(array $data): bool {
        // Expects: 'libelle', 'date_debut', 'date_fin'
        return $this->anneeRepo->create($data);
    }

    /**
     * Met à jour une année académique existante.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateAnnee(int $id, array $data): bool {
        return $this->anneeRepo->update($id, $data);
    }

    /**
     * Supprime une année académique.
     *
     * @param int $id
     * @return bool
     */
    public function deleteAnnee(int $id): bool {
        return $this->anneeRepo->delete($id);
    }
}
