<?php
declare(strict_types=1);

namespace app\Controllers\Public;

use app\Repositories\GroupeRepository;

class GroupeController
{
    protected GroupeRepository $groupeRepository;

    public function __construct()
    {
        $this->groupeRepository = new GroupeRepository();
    }

    /**
     * Renvoie les groupes associés à une université.
     * Attend un paramètre GET "universite_id".
     */
    public function getGroupesByUniversite(): void
    {
        if (!isset($_GET['universite_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "universite_id manquant"]);
            return;
        }
        $universiteId = (int) $_GET['universite_id'];

        try {
            $groupes = $this->groupeRepository->getGroupsByUniversite($universiteId);
            echo json_encode($groupes);
        } catch (\Exception $ex) {
            http_response_code(500);
            echo json_encode(["error" => $ex->getMessage()]);
        }
    }
}
