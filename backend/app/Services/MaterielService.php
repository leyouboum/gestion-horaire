<?php
declare(strict_types=1);

namespace app\Services;

use app\Models\Materiel;
use app\Repositories\MaterielRepository;

class MaterielService {
    protected MaterielRepository $materielRepository;

    public function __construct() {
        $this->materielRepository = new MaterielRepository();
    }

    /**
     * Récupère l'ensemble du matériel et le retourne sous forme d'objets Materiel.
     *
     * @return Materiel[]
     */
    public function getAllMateriel(): array {
        $data = $this->materielRepository->getAllMateriel();
        $materiels = [];
        foreach ($data as $row) {
            $materiels[] = new Materiel(
                (int)$row['id_materiel'],
                $row['type_materiel'],
                (bool)$row['is_mobile'],
                isset($row['id_salle_fixe']) ? (int)$row['id_salle_fixe'] : null,
                isset($row['id_site_affectation']) ? (int)$row['id_site_affectation'] : null,
                $row['salle_fixe'] ?? null,           // nom de la salle
                $row['site_affectation'] ?? null      // nom du site
            );
        }
        return $materiels;
    }

    /**
     * Récupère un matériel par son ID et le retourne sous forme d'objet Materiel.
     *
     * @param int $id
     * @return Materiel|null
     */
    public function getMaterielById(int $id): ?Materiel {
        $row = $this->materielRepository->getMaterielById($id);
        if (!$row) {
            return null;
        }
        return new Materiel(
            (int)$row['id_materiel'],
            $row['type_materiel'],
            (bool)$row['is_mobile'],
            isset($row['id_salle_fixe']) ? (int)$row['id_salle_fixe'] : null,
            isset($row['id_site_affectation']) ? (int)$row['id_site_affectation'] : null,
            $row['salle_fixe'] ?? null,          // nom de la salle
            $row['site_affectation'] ?? null     // nom du site
        );
    }

    /**
     * Crée un nouveau matériel.
     */
    public function createMateriel(array $data): bool {
        if (empty($data['type_materiel']) || !isset($data['is_mobile'])) {
            throw new \InvalidArgumentException("Les informations du matériel sont incomplètes.");
        }
        return $this->materielRepository->createMateriel($data);
    }

    /**
     * Met à jour un matériel existant.
     */
    public function updateMateriel(int $id, array $data): bool {
        return $this->materielRepository->updateMateriel($id, $data);
    }

    /**
     * Supprime un matériel.
     */
    public function deleteMateriel(int $id): bool {
        return $this->materielRepository->deleteMateriel($id);
    }

    /**
     * Récupère le matériel fixe installé dans une salle donnée sous forme d'objets Materiel.
     */
    public function getMaterielBySalle(int $idSalle): array {
        $data = $this->materielRepository->getMaterielBySalle($idSalle);
        $materiels = [];
        foreach ($data as $row) {
            $materiels[] = new Materiel(
                (int)$row['id_materiel'],
                $row['type_materiel'],
                (bool)$row['is_mobile'],
                isset($row['id_salle_fixe']) ? (int)$row['id_salle_fixe'] : null,
                isset($row['id_site_affectation']) ? (int)$row['id_site_affectation'] : null
            );
        }
        return $materiels;
    }

    /**
     * Récupère le matériel mobile affecté à un site donné sous forme d'objets Materiel.
     */
    public function getMobileMaterielBySite(int $siteId): array {
        $data = $this->materielRepository->getMobileMaterielBySite($siteId);
        $materiels = [];
        foreach ($data as $row) {
            $materiels[] = new Materiel(
                (int)$row['id_materiel'],
                $row['type_materiel'],
                (bool)$row['is_mobile'],
                isset($row['id_salle_fixe']) ? (int)$row['id_salle_fixe'] : null,
                isset($row['id_site_affectation']) ? (int)$row['id_site_affectation'] : null
            );
        }
        return $materiels;
    }
}
