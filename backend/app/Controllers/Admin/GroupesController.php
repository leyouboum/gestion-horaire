<?php
declare(strict_types=1);

namespace app\Controllers\Admin;

use app\Repositories\GroupeRepository;

class GroupesController {
    private GroupeRepository $groupeRepo;

    public function __construct() {
        $this->groupeRepo = new GroupeRepository();
    }

    /**
     * Retourne la liste de tous les groupes.
     *
     * @return array
     */
    public function listGroupes(): array {
        return $this->groupeRepo->getAll();
    }

    /**
     * Retourne un groupe par son identifiant.
     *
     * @param int $id
     * @return array|null
     */
    public function getGroupe(int $id): ?array {
        return $this->groupeRepo->getById($id);
    }

    /**
     * Crée un nouveau groupe.
     *
     * @param array $data
     * @return bool
     */
    public function createGroupe(array $data): bool {
        // Expects: 'nom_groupe', 'nb_etudiants', 'id_universite'
        return $this->groupeRepo->create($data);
    }

    /**
     * Met à jour un groupe existant.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateGroupe(int $id, array $data): bool {
        return $this->groupeRepo->update($id, $data);
    }

    /**
     * Supprime un groupe.
     *
     * @param int $id
     * @return bool
     */
    public function deleteGroupe(int $id): bool {
        return $this->groupeRepo->delete($id);
    }

    /**
     * Retourne les groupes associés à un site.
     *
     * @param int $idSite
     * @return array
     */
    public function listGroupesBySite(int $idSite): array {
        return $this->groupeRepo->getGroupesBySite($idSite);
    }

    /**
     * Retourne les groupes d'une université.
     *
     * @param int $universiteId
     * @return array
     */
    public function listGroupesByUniversite(int $universiteId): array {
        return $this->groupeRepo->getGroupsByUniversite($universiteId);
    }
}
