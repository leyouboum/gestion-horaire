<?php
declare(strict_types=1);

namespace app\Controllers\Public;

use app\Repositories\PlanningRepository;
use app\Repositories\SiteRepository;
use app\Repositories\CoursRepository;
use app\Repositories\SalleRepository;

class HoraireController
{
    protected PlanningRepository $planningRepository;
    protected SiteRepository $siteRepository;
    protected CoursRepository $coursRepository;
    protected SalleRepository $salleRepository;

    public function __construct()
    {
        $this->planningRepository = new PlanningRepository();
        $this->siteRepository     = new SiteRepository();
        $this->coursRepository    = new CoursRepository();
        $this->salleRepository    = new SalleRepository();
    }

    /**
     * Renvoie l'emploi du temps complet d'un groupe, incluant les matériels fixes et mobiles.
     * Paramètre GET requis : group_id.
     */
    public function getEmploiDuTemps(): void
    {
        if (!isset($_GET['group_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "group_id manquant"]);
            return;
        }
        $groupId = (int) $_GET['group_id'];

        try {
            $planning = $this->planningRepository->getPlanningByGroup($groupId);
            echo json_encode($planning);
        } catch (\Exception $ex) {
            http_response_code(500);
            echo json_encode(["error" => $ex->getMessage()]);
        }
    }

    /**
     * Renvoie les filtres disponibles pour un groupe (sites, cours, salles).
     * 
     */
    public function getFilters(): void
    {
        if (!isset($_GET['group_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "group_id manquant"]);
            return;
        }
        $groupId = (int) $_GET['group_id'];

        try {
            $sites   = $this->siteRepository->getSitesByGroup($groupId);
            $courses = $this->coursRepository->getCoursByGroup($groupId);
            $classes = $this->salleRepository->getSallesByGroup($groupId);
            echo json_encode([
                "sites"   => $sites,
                "courses" => $courses,
                "classes" => $classes
            ]);
        } catch (\Exception $ex) {
            http_response_code(500);
            echo json_encode(["error" => $ex->getMessage()]);
        }
    }
}
