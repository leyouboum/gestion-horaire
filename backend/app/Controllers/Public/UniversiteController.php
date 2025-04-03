<?php
declare(strict_types=1);

namespace app\Controllers\Public;

use app\Repositories\UniversiteRepository;

class UniversiteController
{
    protected UniversiteRepository $universiteRepository;

    public function __construct()
    {
        $this->universiteRepository = new UniversiteRepository();
    }

    /**
     * Renvoie toutes les universités.
     */
    public function getAllUniversites(): void
    {
        try {
            $universites = $this->universiteRepository->getAll();
            echo json_encode($universites);
        } catch (\Exception $ex) {
            http_response_code(500);
            echo json_encode(["error" => $ex->getMessage()]);
        }
    }
}
