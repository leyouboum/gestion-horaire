<?php
declare(strict_types=1);

namespace app\Controllers\Admin;

use app\Repositories\SiteRepository;

class SiteController {
    private SiteRepository $siteRepo;

    public function __construct() {
        $this->siteRepo = new SiteRepository();
    }

    /**
     * Retourne la liste de tous les sites.
     *
     * @return array
     */
    public function listSites(): array {
        return $this->siteRepo->getAllSites();
    }

    /**
     * Retourne un site par son ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getSite(int $id): ?array {
        return $this->siteRepo->getSiteById($id);
    }

    /**
     * Retourne les sites d'une université.
     *
     * @param int $univId
     * @return array
     */
    public function listSitesByUniversite(int $univId): array {
        return $this->siteRepo->getByUniversite($univId);
    }

    /**
     * Crée un nouveau site.
     *
     * @param array $data
     * @return bool
     */
    public function createSite(array $data): bool {
        return $this->siteRepo->createSite($data);
    }

    /**
     * Met à jour un site existant.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateSite(int $id, array $data): bool {
        return $this->siteRepo->updateSite($id, $data);
    }

    /**
     * Supprime un site.
     *
     * @param int $id
     * @return bool
     */
    public function deleteSite(int $id): bool {
        return $this->siteRepo->deleteSite($id);
    }
}
