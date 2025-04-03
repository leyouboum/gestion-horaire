<?php
declare(strict_types=1);

namespace app\Controllers\Admin;

use app\Repositories\MaterielRepository;

class MaterielController {
    private MaterielRepository $materielRepo;

    public function __construct() {
        $this->materielRepo = new MaterielRepository();
    }

    /**
     * Retourne la liste de tout le matériel.
     *
     * @return array
     */
    public function listMateriels(): array {
        return $this->materielRepo->getAllMateriel();
    }

    /**
     * Retourne un matériel par son ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getMateriel(int $id): ?array {
        return $this->materielRepo->getMaterielById($id);
    }

    /**
     * Crée un nouveau matériel.
     *
     * @param array $data
     * @return bool
     */
    public function createMateriel(array $data): bool {
        return $this->materielRepo->createMateriel($data);
    }

    /**
     * Met à jour un matériel existant.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateMateriel(int $id, array $data): bool {
        return $this->materielRepo->updateMateriel($id, $data);
    }

    /**
     * Supprime un matériel.
     *
     * @param int $id
     * @return bool
     */
    public function deleteMateriel(int $id): bool {
        return $this->materielRepo->deleteMateriel($id);
    }
    
    /**
     * Retourne la liste des matériels mobiles affectés à un site.
     *
     * @param int $siteId
     * @return array
     */
    public function listMobileMaterielBySite(int $siteId): array {
        return $this->materielRepo->getMobileMaterielBySite($siteId);
    }
}
