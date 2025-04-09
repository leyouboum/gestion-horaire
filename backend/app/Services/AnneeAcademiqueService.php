<?php
declare(strict_types=1);

namespace app\Services;

use app\Models\AnneeAcademique;
use app\Repositories\AnneeAcademiqueRepository;

class AnneeAcademiqueService {
    protected AnneeAcademiqueRepository $anneeRepo;

    public function __construct() {
        $this->anneeRepo = new AnneeAcademiqueRepository();
    }

    /**
     * Récupère toutes les années académiques et les transforme en objets AnneeAcademique.
     *
     * @return AnneeAcademique[]
     */
    public function getAllAnnees(): array {
        $data = $this->anneeRepo->getAll();
        $annees = [];
        foreach ($data as $row) {
            $annees[] = new AnneeAcademique(
                (int)$row['id_annee'],
                $row['libelle'],
                new \DateTime($row['date_debut']),
                new \DateTime($row['date_fin'])
            );
        }
        return $annees;
    }

    /**
     * Récupère une année académique par son ID et le transforme en objet AnneeAcademique.
     *
     * @param int $id
     * @return AnneeAcademique|null
     */
    public function getAnneeById(int $id): ?AnneeAcademique {
        $data = $this->anneeRepo->getById($id);
        if (!$data) {
            return null;
        }
        return new AnneeAcademique(
            (int)$data['id_annee'],
            $data['libelle'],
            new \DateTime($data['date_debut']),
            new \DateTime($data['date_fin'])
        );
    }

    /**
     * Crée une nouvelle année académique.
     *
     * @param array $data
     * @return bool
     */
    public function createAnnee(array $data): bool {
        if (empty($data['libelle']) || empty($data['date_debut']) || empty($data['date_fin'])) {
            throw new \InvalidArgumentException("Les informations de l'année académique sont incomplètes.");
        }
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
