<?php
declare(strict_types=1);

namespace app\Services;

use app\Models\Groupe;
use app\Repositories\GroupeRepository;

class GroupeService {
    protected GroupeRepository $groupeRepository;

    public function __construct() {
        $this->groupeRepository = new GroupeRepository();
    }

    public function getAllGroups(): array {
        $data = $this->groupeRepository->getAll();
        $groups = [];
        foreach ($data as $row) {
            $groups[] = new Groupe(
                (int)$row['id_groupe'],
                $row['nom_groupe'],
                (int)$row['nb_etudiants']
            );
        }
        return $groups;
    }

    public function getGroupById(int $id): ?Groupe {
        $data = $this->groupeRepository->getById($id);
        if (!$data) {
            return null;
        }
        return new Groupe(
            (int)$data['id_groupe'],
            $data['nom_groupe'],
            (int)$data['nb_etudiants']
        );
    }

    public function createGroup(array $data): bool {
        if (empty($data['nom_groupe']) || empty($data['nb_etudiants']) || empty($data['id_universite'])) {
            throw new \InvalidArgumentException("Les informations du groupe sont incomplètes.");
        }
        return $this->groupeRepository->create($data);
    }

    public function updateGroup(int $id, array $data): bool {
        return $this->groupeRepository->update($id, $data);
    }

    public function deleteGroup(int $id): bool {
        return $this->groupeRepository->delete($id);
    }

    public function getGroupsBySite(int $idSite): array {
        $data = $this->groupeRepository->getGroupesBySite($idSite);
        $groups = [];
        foreach ($data as $row) {
            $groups[] = new Groupe(
                (int)$row['id_groupe'],
                $row['nom_groupe'],
                (int)$row['nb_etudiants']
            );
        }
        return $groups;
    }

    public function getGroupsByUniversite(int $universiteId): array {
        $data = $this->groupeRepository->getGroupsByUniversite($universiteId);
        $groups = [];
        foreach ($data as $row) {
            $groups[] = new Groupe(
                (int)$row['id_groupe'],
                $row['nom_groupe'],
                (int)$row['nb_etudiants']
            );
        }
        return $groups;
    }
}
