<?php
declare(strict_types=1);

namespace app\Repositories;

use PDO;
use app\Config\Database;

class CoursRepository {
    protected PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Récupère tous les cours avec les noms des sites affectés.
     *
     * @return array
     */
    public function getAllCours(): array {
        $sql = "
            SELECT c.*, 
            GROUP_CONCAT(s.id_site SEPARATOR ',') AS siteIds,
            GROUP_CONCAT(s.nom SEPARATOR ', ') AS sites 
            FROM cours c 
            LEFT JOIN cours_site cs ON c.id_cours = cs.id_cours 
            LEFT JOIN site s ON cs.id_site = s.id_site 
            GROUP BY c.id_cours
            ORDER BY c.id_cours DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère un cours par son ID, avec la liste des sites associés.
     *
     * @param int $id
     * @return array|null
     */
    public function getCoursById(int $id): ?array {
        $sql = "
            SELECT c.*, GROUP_CONCAT(s.nom SEPARATOR ', ') AS sites
            FROM cours c
            LEFT JOIN cours_site cs ON c.id_cours = cs.id_cours
            LEFT JOIN site s ON cs.id_site = s.id_site
            WHERE c.id_cours = ?
            GROUP BY c.id_cours LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Récupère les cours associés à un groupe donné.
     *
     * @param int $groupId
     * @return array
     */
    public function getCoursByGroup(int $groupId): array {
        $sql = "
            SELECT DISTINCT c.id_cours, c.nom_cours
            FROM planning p
            JOIN cours c ON p.id_cours = c.id_cours
            WHERE p.id_groupe = ?
            GROUP BY c.id_cours
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$groupId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère les cours associés à un site donné.
     *
     * @param int $idSite
     * @return array
     */
    public function getBySite(int $idSite): array {
        $sql = "
            SELECT c.*, GROUP_CONCAT(s.nom SEPARATOR ', ') AS sites 
            FROM cours c
            JOIN cours_site cs ON c.id_cours = cs.id_cours
            JOIN site s ON cs.id_site = s.id_site
            WHERE s.id_site = :idSite
            GROUP BY c.id_cours
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idSite' => $idSite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Crée un nouveau cours et enregistre ses affectations de sites.
     *
     * @param array $data
     * @return bool
     */
    public function createCours(array $data): bool {
        $stmt = $this->pdo->prepare("INSERT INTO cours (code_cours, nom_cours, details, duree) VALUES (?, ?, ?, ?)");
        $res = $stmt->execute([
            $data['code_cours'],
            $data['nom_cours'],
            $data['details'] ?? null,
            (int)$data['duree']
        ]);
        if ($res) {
            $coursId = (int)$this->pdo->lastInsertId();
            if (isset($data['sites']) && is_array($data['sites'])) {
                foreach ($data['sites'] as $siteId) {
                    $stmtAssign = $this->pdo->prepare("INSERT IGNORE INTO cours_site (id_cours, id_site) VALUES (?, ?)");
                    $stmtAssign->execute([$coursId, $siteId]);
                }
            }
        }
        return $res;
    }

    /**
     * Met à jour un cours et ses affectations de sites.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateCours(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("UPDATE cours SET code_cours = ?, nom_cours = ?, details = ?, duree = ? WHERE id_cours = ?");
        $res = $stmt->execute([
            $data['code_cours'],
            $data['nom_cours'],
            $data['details'] ?? null,
            (int)$data['duree'],
            $id
        ]);
        if ($res) {
            $stmtDel = $this->pdo->prepare("DELETE FROM cours_site WHERE id_cours = ?");
            $stmtDel->execute([$id]);
            if (isset($data['sites']) && is_array($data['sites'])) {
                foreach ($data['sites'] as $siteId) {
                    $stmtAssign = $this->pdo->prepare("INSERT IGNORE INTO cours_site (id_cours, id_site) VALUES (?, ?)");
                    $stmtAssign->execute([$id, $siteId]);
                }
            }
        }
        return $res;
    }

    /**
     * Supprime un cours.
     *
     * @param int $id
     * @return bool
     */
    public function deleteCours(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM cours WHERE id_cours = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Associe un cours à un groupe via la table de jointure cours_groupe.
     *
     * @param int $coursId
     * @param int $groupId
     * @return bool
     */
    public function assignCoursToGroup(int $coursId, int $groupId): bool {
        $stmt = $this->pdo->prepare("INSERT INTO cours_groupe (id_cours, id_groupe) VALUES (?, ?)");
        return $stmt->execute([$coursId, $groupId]);
    }

    /**
     * Retire l'association d'un cours à un groupe.
     *
     * @param int $coursId
     * @param int $groupId
     * @return bool
     */
    public function removeCoursFromGroup(int $coursId, int $groupId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM cours_groupe WHERE id_cours = ? AND id_groupe = ?");
        return $stmt->execute([$coursId, $groupId]);
    }
}
