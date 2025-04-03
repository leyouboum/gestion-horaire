<?php
declare(strict_types=1);

namespace app\Services;

use app\Models\Site;
use app\Repositories\SiteRepository;

class SiteService {
    protected SiteRepository $siteRepository;

    public function __construct() {
        $this->siteRepository = new SiteRepository();
    }

    /**
     * Récupère tous les sites et les transforme en objets Site.
     *
     * @return Site[]
     */
    public function getAllSites(): array {
        $data = $this->siteRepository->getAllSites();
        $sites = [];
        foreach ($data as $row) {
            $sites[] = new Site(
                isset($row['id_site']) ? (int)$row['id_site'] : null,
                (int)$row['id_universite'],
                $row['nom'],
                $row['heure_ouverture'],
                $row['heure_fermeture']
            );
        }
        return $sites;
    }

    /**
     * Récupère un site par son ID et le transforme en objet Site.
     *
     * @param int $id
     * @return Site|null
     */
    public function getSiteById(int $id): ?Site {
        $data = $this->siteRepository->getSiteById($id);
        if (!$data) {
            return null;
        }
        return new Site(
            isset($data['id_site']) ? (int)$data['id_site'] : null,
            (int)$data['id_universite'],
            $data['nom'],
            $data['heure_ouverture'],
            $data['heure_fermeture']
        );
    }

    /**
     * Crée un nouveau site.
     *
     * @param array $data
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function createSite(array $data): bool {
        if (empty($data['id_universite']) || empty($data['nom']) || empty($data['heure_ouverture']) || empty($data['heure_fermeture'])) {
            throw new \InvalidArgumentException("Les informations du site sont incomplètes.");
        }
        return $this->siteRepository->createSite($data);
    }

    /**
     * Met à jour un site existant.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateSite(int $id, array $data): bool {
        return $this->siteRepository->updateSite($id, $data);
    }

    /**
     * Supprime un site.
     *
     * @param int $id
     * @return bool
     */
    public function deleteSite(int $id): bool {
        return $this->siteRepository->deleteSite($id);
    }

    /**
     * Récupère les sites d'une université.
     *
     * @param int $univId
     * @return Site[]
     */
    public function getByUniversite(int $univId): array {
        $data = $this->siteRepository->getByUniversite($univId);
        $sites = [];
        foreach ($data as $row) {
            $sites[] = new Site(
                isset($row['id_site']) ? (int)$row['id_site'] : null,
                (int)$row['id_universite'],
                $row['nom'],
                $row['heure_ouverture'],
                $row['heure_fermeture']
            );
        }
        return $sites;
    }

    /**
     * Récupère les sites associés à un groupe.
     *
     * @param int $groupId
     * @return Site[]
     */
    public function getSitesByGroup(int $groupId): array {
        $data = $this->siteRepository->getSitesByGroup($groupId);
        $sites = [];
        foreach ($data as $row) {
            $sites[] = new Site(
                isset($row['id_site']) ? (int)$row['id_site'] : null,
                (int)$row['id_universite'],
                $row['nom'],
                $row['heure_ouverture'],
                $row['heure_fermeture']
            );
        }
        return $sites;
    }
}
