<?php
declare(strict_types=1);

namespace app\Controllers\Admin;

use app\Repositories\MaterielRepository;
// OU éventuellement : use app\Services\MaterielService;

class MaterielController {
    private MaterielRepository $materielRepo;
    // ou private MaterielService $materielService;

    public function __construct() {
        $this->materielRepo = new MaterielRepository();
        // ou $this->materielService = new MaterielService();
    }

    /**
     * Retourne la liste de tout le matériel.
     */
    public function listMateriels(): array {
        // Retour brut depuis le repository
        return $this->materielRepo->getAllMateriel();

        // OU si vous voulez la liste d'objets Materiel en JSON :
        // $materiels = $this->materielService->getAllMateriel();
        // return array_map(fn($m) => $m->jsonSerialize(), $materiels);
    }

    /**
     * Retourne un matériel par son identifiant.
     *
     * @param int $id
     * @return array|null
     */
    public function getMateriel(int $id): ?array {
        return $this->materielRepo->getMaterielById($id);

        // OU via le service :
        // $materiel = $this->materielService->getMaterielById($id);
        // return $materiel ? $materiel->jsonSerialize() : null;
    }

    /**
     * Crée un nouveau matériel.
     */
    public function createMateriel(array $data): bool {
        return $this->materielRepo->createMateriel($data);
        // OU via le service : return $this->materielService->createMateriel($data);
    }

    /**
     * Met à jour un matériel existant.
     */
    public function updateMateriel(int $id, array $data): bool {
        return $this->materielRepo->updateMateriel($id, $data);
        // OU via le service : return $this->materielService->updateMateriel($id, $data);
    }

    /**
     * Supprime un matériel.
     */
    public function deleteMateriel(int $id): bool {
        return $this->materielRepo->deleteMateriel($id);
        // OU via le service : return $this->materielService->deleteMateriel($id);
    }
    
    /**
     * Retourne les matériels mobiles affectés à un site.
     *
     * @param int $siteId
     * @return array
     */
    public function listMobileMaterielBySite(int $siteId): array {
        return $this->materielRepo->getMobileMaterielBySite($siteId);
        // OU via le service : return $this->materielService->getMobileMaterielBySite($siteId);
    }
}
