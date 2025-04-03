<?php
declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__.'/../config/database.php';

require_once __DIR__.'/../app/Controllers/Public/HoraireController.php';
require_once __DIR__.'/../app/Controllers/Public/UniversiteController.php';
require_once __DIR__.'/../app/Controllers/Public/GroupeController.php';

require_once __DIR__.'/../app/Repositories/PlanningRepository.php';
require_once __DIR__.'/../app/Repositories/SiteRepository.php';
require_once __DIR__.'/../app/Repositories/CoursRepository.php';
require_once __DIR__.'/../app/Repositories/SalleRepository.php';
require_once __DIR__.'/../app/Repositories/GroupeRepository.php';
require_once __DIR__.'/../app/Repositories/UniversiteRepository.php';
require_once __DIR__.'/../app/Repositories/MaterielRepository.php';

require_once __DIR__.'/../app/Models/Universite.php';
require_once __DIR__.'/../app/Models/Site.php';
require_once __DIR__.'/../app/Models/Cours.php';
require_once __DIR__.'/../app/Models/Salle.php';
require_once __DIR__.'/../app/Models/Groupe.php';
require_once __DIR__.'/../app/Models/Planning.php';
require_once __DIR__.'/../app/Models/Materiel.php';

use app\Controllers\Public\HoraireController;
use app\Controllers\Public\UniversiteController;
use app\Controllers\Public\GroupeController;

$horaireController    = new HoraireController();
$universiteController = new UniversiteController();
$groupeController     = new GroupeController();

$action = $_GET['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        case 'universites':
            if ($method === 'GET') {
                $universiteController->getAllUniversites();
            } else {
                http_response_code(405);
                echo json_encode(["error" => "Méthode non autorisée"]);
            }
            break;
        case 'groupes_by_univ':
            if ($method === 'GET') {
                $groupeController->getGroupesByUniversite();
            } else {
                http_response_code(405);
                echo json_encode(["error" => "Méthode non autorisée"]);
            }
            break;
        case 'filters':
            if ($method === 'GET') {
                $horaireController->getFilters();
            } else {
                http_response_code(405);
                echo json_encode(["error" => "Méthode non autorisée"]);
            }
            break;
        case 'emploi_du_temps':
            if ($method === 'GET') {
                $horaireController->getEmploiDuTemps();
            } else {
                http_response_code(405);
                echo json_encode(["error" => "Méthode non autorisée"]);
            }
            break;
        default:
            http_response_code(400);
            echo json_encode(["error" => "Action inconnue ou manquante"]);
            break;
    }
} catch (\Exception $ex) {
    http_response_code(500);
    echo json_encode(["error" => $ex->getMessage()]);
}
