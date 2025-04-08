<?php
declare(strict_types=1);

namespace app\Repositories;

use PDO;
use app\Config\Database;

class MaterielRepository {
    protected PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Récupère tout le matériel, en joignant éventuellement le nom de la salle et le nom du site.
     */
    public function getAllMateriel(): array {
        $sql = "
            SELECT m.*,
                   s.nom_salle AS salle_fixe,
                   si.nom AS site_affectation
            FROM materiel m
            LEFT JOIN salle s ON m.id_salle_fixe = s.id_salle
            LEFT JOIN site si ON m.id_site_affectation = si.id_site
            ORDER BY m.id_materiel DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère un seul matériel par ID, avec les mêmes champs que la liste.
     */
    public function getMaterielById(int $id): ?array {
        $sql = "
            SELECT m.*,
                   s.nom_salle AS salle_fixe,
                   si.nom AS site_affectation
            FROM materiel m
            LEFT JOIN salle s ON m.id_salle_fixe = s.id_salle
            LEFT JOIN site si ON m.id_site_affectation = si.id_site
            WHERE m.id_materiel = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Crée un nouveau matériel.
     */
    public function createMateriel(array $data): bool {
        $sql = "INSERT INTO materiel (type_materiel, is_mobile, id_salle_fixe, id_site_affectation)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['type_materiel'],
            $data['is_mobile'],
            $data['id_salle_fixe'] ?? null,
            $data['id_site_affectation'] ?? null
        ]);
    }

    /**
     * Met à jour un matériel existant.
     */
    public function updateMateriel(int $id, array $data): bool {
        $sql = "UPDATE materiel
                SET type_materiel = ?,
                    is_mobile = ?,
                    id_salle_fixe = ?,
                    id_site_affectation = ?
                WHERE id_materiel = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['type_materiel'],
            $data['is_mobile'],
            $data['id_salle_fixe'] ?? null,
            $data['id_site_affectation'] ?? null,
            $id
        ]);
    }

    /**
     * Supprime un matériel.
     */
    public function deleteMateriel(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM materiel WHERE id_materiel = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Récupère le matériel fixe installé dans une salle donnée.
     */
    public function getMaterielBySalle(int $idSalle): array {
        $stmt = $this->pdo->prepare("SELECT * FROM materiel WHERE id_salle_fixe = ?");
        $stmt->execute([$idSalle]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Récupère le matériel mobile affecté à un site donné.
     */
    public function getMobileMaterielBySite(int $siteId): array {
        $sql = "SELECT * FROM materiel WHERE is_mobile = 1 AND id_site_affectation = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$siteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
