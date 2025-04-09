<?php
declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__.'/../config/database.php';

// Admin Controllers
require_once __DIR__.'/../app/Controllers/Admin/CoursController.php';
require_once __DIR__.'/../app/Controllers/Admin/GroupesController.php';
require_once __DIR__.'/../app/Controllers/Admin/PlanningController.php';
require_once __DIR__.'/../app/Controllers/Admin/SalleController.php';
require_once __DIR__.'/../app/Controllers/Admin/SiteController.php';
require_once __DIR__.'/../app/Controllers/Admin/UniversiteController.php';
require_once __DIR__.'/../app/Controllers/Admin/MaterielController.php';
require_once __DIR__.'/../app/Controllers/Admin/AnneeAcademiqueController.php';

// Repositories
require_once __DIR__.'/../app/Repositories/CoursRepository.php';
require_once __DIR__.'/../app/Repositories/GroupeRepository.php';
require_once __DIR__.'/../app/Repositories/PlanningRepository.php';
require_once __DIR__.'/../app/Repositories/SalleRepository.php';
require_once __DIR__.'/../app/Repositories/SiteRepository.php';
require_once __DIR__.'/../app/Repositories/UniversiteRepository.php';
require_once __DIR__.'/../app/Repositories/MaterielRepository.php';
require_once __DIR__.'/../app/Repositories/AnneeAcademiqueRepository.php';

// Models
require_once __DIR__.'/../app/Models/Universite.php';
require_once __DIR__.'/../app/Models/Site.php';
require_once __DIR__.'/../app/Models/Cours.php';
require_once __DIR__.'/../app/Models/Salle.php';
require_once __DIR__.'/../app/Models/Groupe.php';
require_once __DIR__.'/../app/Models/Planning.php';
require_once __DIR__.'/../app/Models/Materiel.php';
require_once __DIR__.'/../app/Models/AnneeAcademique.php';


use app\Controllers\Admin\CoursController;
use app\Controllers\Admin\GroupesController;
use app\Controllers\Admin\PlanningController;
use app\Controllers\Admin\SalleController;
use app\Controllers\Admin\SiteController;
use app\Controllers\Admin\UniversiteController;
use app\Controllers\Admin\MaterielController;
use app\Controllers\Admin\AnneeAcademiqueController;

$coursController       = new CoursController();
$groupesController     = new GroupesController();
$planningController    = new PlanningController();
$salleController       = new SalleController();
$siteController        = new SiteController();
$universiteController  = new UniversiteController();
$materielController    = new MaterielController();
$anneeController       = new AnneeAcademiqueController();

$entity = $_GET['entity'] ?? null;
$action = $_GET['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($entity) {
        case 'cours':
            switch ($method) {
                case 'GET':
                    if ($action === 'list') {
                        echo json_encode($coursController->listCours());
                    } elseif ($action === 'get' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        $result = $coursController->getCours($id);
                        if ($result) {
                            echo json_encode($result);
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Cours introuvable"]);
                        }
                    } elseif ($action === 'listBySite' && isset($_GET['siteId'])) {
                        $siteId = (int) $_GET['siteId'];
                        echo json_encode($coursController->listCoursBySite($siteId));
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action GET non reconnue pour 'cours'"]);
                    }
                    break;
                case 'POST':
                    if ($action === 'create') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $coursController->createCours($data)) {
                            http_response_code(201);
                            echo json_encode(["message" => "Cours créé avec succès"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la création du cours"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action POST non reconnue pour 'cours'"]);
                    }
                    break;
                case 'PUT':
                    if ($action === 'update' && isset($_GET['id'])) {
                        $id = (int) $_GET['id'];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $coursController->updateCours($id, $data)) {
                            echo json_encode(["message" => "Cours mis à jour"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la mise à jour du cours"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action PUT non reconnue pour 'cours'"]);
                    }
                    break;
                case 'DELETE':
                    if ($action === 'delete' && isset($_GET['id'])) {
                        $id = (int) $_GET['id'];
                        if ($coursController->deleteCours($id)) {
                            echo json_encode(["message" => "Cours supprimé"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la suppression du cours"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action DELETE non reconnue pour 'cours'"]);
                    }
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(["error" => "Méthode non supportée pour 'cours'"]);
            }
            break;

        case 'groupes':
            switch ($method) {
                case 'GET':
                    if ($action === 'list') {
                        echo json_encode($groupesController->listGroupes());
                    } elseif ($action === 'get' && isset($_GET['id'])) {
                        $id = (int) $_GET['id'];
                        $result = $groupesController->getGroupe($id);
                        if ($result) {
                            echo json_encode($result);
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Groupe introuvable"]);
                        }
                    } elseif ($action === 'listBySite' && isset($_GET['siteId'])) {
                        $siteId = (int)$_GET['siteId'];
                        echo json_encode($groupesController->listGroupesBySite($siteId));
                    } elseif ($action === 'listByUniversite' && isset($_GET['univId'])) {
                        $univId = (int)$_GET['univId'];
                        echo json_encode($groupesController->listGroupesByUniversite($univId));
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action GET non reconnue pour 'groupes'"]);
                    }
                    break;
                case 'POST':
                    if ($action === 'create') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $groupesController->createGroupe($data)) {
                            http_response_code(201);
                            echo json_encode(["message" => "Groupe créé avec succès"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la création du groupe"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action POST non reconnue pour 'groupes'"]);
                    }
                    break;
                case 'PUT':
                    if ($action === 'update' && isset($_GET['id'])) {
                        $id = (int) $_GET['id'];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $groupesController->updateGroupe($id, $data)) {
                            echo json_encode(["message" => "Groupe mis à jour"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la mise à jour du groupe"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action PUT non reconnue pour 'groupes'"]);
                    }
                    break;
                case 'DELETE':
                    if ($action === 'delete' && isset($_GET['id'])) {
                        $id = (int) $_GET['id'];
                        if ($groupesController->deleteGroupe($id)) {
                            echo json_encode(["message" => "Groupe supprimé"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la suppression du groupe"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action DELETE non reconnue pour 'groupes'"]);
                    }
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(["error" => "Méthode non supportée pour 'groupes'"]);
            }
            break;

        case 'planning':
            switch ($method) {
                case 'GET':
                    if ($action === 'list') {
                        // Lister par groupId avec dates optionnelles ET possibilité de filtrer par année
                        if (isset($_GET['groupId']) && is_numeric($_GET['groupId'])) {
                            $groupId = (int) $_GET['groupId'];
                            $startDate = $_GET['startDate'] ?? null;
                            $endDate   = $_GET['endDate'] ?? null;
                            if (isset($_GET['annee_id']) && is_numeric($_GET['annee_id'])) {
                                $anneeId = (int) $_GET['annee_id'];
                                echo json_encode($planningController->getPlanningByGroupAndAnnee($groupId, $anneeId, $startDate, $endDate));
                            } else {
                                echo json_encode($planningController->listPlanningsByGroup($groupId, $startDate, $endDate));
                            }
                        } else {
                            http_response_code(400);
                            echo json_encode(["error" => "Le paramètre groupId est requis pour lister les plannings"]);
                        }
                    } elseif ($action === 'listAll') {
                        echo json_encode($planningController->listAllPlannings());
                    } elseif ($action === 'get' && isset($_GET['id'])) {
                        $id = (int) $_GET['id'];
                        $result = $planningController->getPlanning($id);
                        if ($result) {
                            echo json_encode($result);
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Planning introuvable"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action GET non reconnue pour 'planning'"]);
                    }
                    break;
                case 'POST':
                    if ($action === 'create') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $planningController->createPlanning($data)) {
                            http_response_code(201);
                            echo json_encode(["message" => "Créneau créé avec succès"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la création du créneau"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action POST non reconnue pour 'planning'"]);
                    }
                    break;
                case 'PUT':
                    if ($action === 'update' && isset($_GET['id'])) {
                        $id = (int) $_GET['id'];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $planningController->updatePlanning($id, $data)) {
                            echo json_encode(["message" => "Créneau mis à jour"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la mise à jour du créneau"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action PUT non reconnue pour 'planning'"]);
                    }
                    break;
                case 'DELETE':
                    if ($action === 'delete' && isset($_GET['id'])) {
                        $id = (int) $_GET['id'];
                        if ($planningController->deletePlanning($id)) {
                            echo json_encode(["message" => "Créneau supprimé"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la suppression du créneau"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action DELETE non reconnue pour 'planning'"]);
                    }
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(["error" => "Méthode non supportée pour 'planning'"]);
            }
            break;
    

        case 'salles':
            switch ($method) {
                case 'GET':
                    if ($action === 'list') {
                        echo json_encode($salleController->listSalles());
                    } elseif ($action === 'get' && isset($_GET['id'])) {
                        $id = (int) $_GET['id'];
                        $result = $salleController->getSalle($id);
                        if ($result) {
                            echo json_encode($result);
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Salle introuvable"]);
                        }
                    } elseif ($action === 'listBySite' && isset($_GET['siteId'])) {
                        $siteId = (int)$_GET['siteId'];
                        echo json_encode($salleController->listSallesBySite($siteId));
                    } elseif ($action === 'listByGroup' && isset($_GET['groupId'])) {
                        $groupId = (int) $_GET['groupId'];
                        echo json_encode($salleController->listSallesByGroup($groupId));
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action GET non reconnue pour 'salles'"]);
                    }
                    break;
                case 'POST':
                    if ($action === 'create') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $salleController->createSalle($data)) {
                            http_response_code(201);
                            echo json_encode(["message" => "Salle créée avec succès"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la création de la salle"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action POST non reconnue pour 'salles'"]);
                    }
                    break;
                case 'PUT':
                    if ($action === 'update' && isset($_GET['id'])) {
                        $id = (int) $_GET['id'];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $salleController->updateSalle($id, $data)) {
                            echo json_encode(["message" => "Salle mise à jour"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la mise à jour de la salle"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action PUT non reconnue pour 'salles'"]);
                    }
                    break;
                case 'DELETE':
                    if ($action === 'delete' && isset($_GET['id'])) {
                        $id = (int) $_GET['id'];
                        if ($salleController->deleteSalle($id)) {
                            echo json_encode(["message" => "Salle supprimée"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la suppression de la salle"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action DELETE non reconnue pour 'salles'"]);
                    }
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(["error" => "Méthode non supportée pour 'salles'"]);
            }
            break;

        case 'sites':
            switch ($method) {
                case 'GET':
                    if ($action === 'list') {
                        echo json_encode($siteController->listSites());
                    } elseif ($action === 'get' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        $result = $siteController->getSite($id);
                        if ($result) {
                            echo json_encode($result);
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Site introuvable"]);
                        }
                    } elseif ($action === 'listByUniversite' && isset($_GET['univId'])) {
                        $univId = (int)$_GET['univId'];
                        echo json_encode($siteController->listSitesByUniversite($univId));
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action GET non reconnue pour 'sites'"]);
                    }
                    break;
                case 'POST':
                    if ($action === 'create') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $siteController->createSite($data)) {
                            http_response_code(201);
                            echo json_encode(["message" => "Site créé avec succès"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la création du site"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action POST non reconnue pour 'sites'"]);
                    }
                    break;
                case 'PUT':
                    if ($action === 'update' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $siteController->updateSite($id, $data)) {
                            echo json_encode(["message" => "Site mis à jour"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la mise à jour du site"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action PUT non reconnue pour 'sites'"]);
                    }
                    break;
                case 'DELETE':
                    if ($action === 'delete' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        if ($siteController->deleteSite($id)) {
                            echo json_encode(["message" => "Site supprimé"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la suppression du site"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action DELETE non reconnue pour 'sites'"]);
                    }
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(["error" => "Méthode non supportée pour 'sites'"]);
            }
            break;

        case 'universites':
            switch ($method) {
                case 'GET':
                    if ($action === 'list') {
                        echo json_encode($universiteController->listUniversites());
                    } elseif ($action === 'get' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        $result = $universiteController->getUniversite($id);
                        if ($result) {
                            echo json_encode($result);
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Université introuvable"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action GET non reconnue pour 'universites'"]);
                    }
                    break;
                case 'POST':
                    if ($action === 'create') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $universiteController->createUniversite($data)) {
                            http_response_code(201);
                            echo json_encode(["message" => "Université créée avec succès"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la création de l'université"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action POST non reconnue pour 'universites'"]);
                    }
                    break;
                case 'PUT':
                    if ($action === 'update' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $universiteController->updateUniversite($id, $data)) {
                            echo json_encode(["message" => "Université mise à jour"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la mise à jour de l'université"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action PUT non reconnue pour 'universites'"]);
                    }
                    break;
                case 'DELETE':
                    if ($action === 'delete' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        if ($universiteController->deleteUniversite($id)) {
                            echo json_encode(["message" => "Université supprimée"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la suppression de l'université"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action DELETE non reconnue pour 'universites'"]);
                    }
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(["error" => "Méthode non supportée pour 'universites'"]);
            }
            break;

        case 'materiels':
            switch ($method) {
                case 'GET':
                    if ($action === 'list') {
                        echo json_encode($materielController->listMateriels());
                    } elseif ($action === 'get' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        $result = $materielController->getMateriel($id);
                        if ($result) {
                            echo json_encode($result);
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Matériel introuvable"]);
                        }
                    } elseif ($action === 'listMobileBySite' && isset($_GET['siteId'])) {
                        $siteId = (int)$_GET['siteId'];
                        echo json_encode($materielController->listMobileMaterielBySite($siteId));
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action GET non reconnue pour 'materiels'"]);
                    }
                    break;
                case 'POST':
                    if ($action === 'create') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $materielController->createMateriel($data)) {
                            http_response_code(201);
                            echo json_encode(["message" => "Matériel créé avec succès"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la création du matériel"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action POST non reconnue pour 'materiels'"]);
                    }
                    break;
                case 'PUT':
                    if ($action === 'update' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $materielController->updateMateriel($id, $data)) {
                            echo json_encode(["message" => "Matériel mis à jour"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la mise à jour du matériel"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action PUT non reconnue pour 'materiels'"]);
                    }
                    break;
                case 'DELETE':
                    if ($action === 'delete' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        if ($materielController->deleteMateriel($id)) {
                            echo json_encode(["message" => "Matériel supprimé"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la suppression du matériel"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action DELETE non reconnue pour 'materiels'"]);
                    }
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(["error" => "Méthode non supportée pour 'materiels'"]);
            }
            break;
        
        case 'annees':
            switch ($method) {
                case 'GET':
                    if ($action === 'list') {
                        // Récupère la liste complète des années académiques
                        echo json_encode($anneeController->listAnnees());
                    } elseif ($action === 'get' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        $result = $anneeController->getAnnee($id);
                        if ($result) {
                            echo json_encode($result);
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Année académique introuvable"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action GET non reconnue pour 'annees'"]);
                    }
                    break;
                case 'POST':
                    if ($action === 'create') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $anneeController->createAnnee($data)) {
                            http_response_code(201);
                            echo json_encode(["message" => "Année académique créée avec succès"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la création de l'année académique"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action POST non reconnue pour 'annees'"]);
                    }
                    break;
                case 'PUT':
                    if ($action === 'update' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data && $anneeController->updateAnnee($id, $data)) {
                            echo json_encode(["message" => "Année académique mise à jour"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la mise à jour de l'année académique"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action PUT non reconnue pour 'annees'"]);
                    }
                    break;
                case 'DELETE':
                    if ($action === 'delete' && isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                        if ($anneeController->deleteAnnee($id)) {
                            echo json_encode(["message" => "Année académique supprimée"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Erreur lors de la suppression de l'année académique"]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Action DELETE non reconnue pour 'annees'"]);
                    }
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(["error" => "Méthode non supportée pour 'annees'"]);
            }
            break;
            

        default:
            http_response_code(400);
            echo json_encode([
                "error" => "Entité inconnue. Les entités supportées sont 'cours', 'groupes', 'planning', 'salles', 'sites', 'universites' et 'materiels'"
            ]);
    }
} catch (\Exception $ex) {
    http_response_code(500);
    echo json_encode(["error" => $ex->getMessage()]);
}
