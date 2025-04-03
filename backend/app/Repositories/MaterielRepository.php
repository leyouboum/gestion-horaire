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

    // Récupère tout le matériel en joignant le site d'affectation
    public function getAllMateriel(): array {
        $sql = "
            SELECT m.*, s.nom AS site_affectation
            FROM materiel m
            LEFT JOIN site s ON m.id_site_affectation = s.id_site
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Récupère un matériel par son ID
    public function getMaterielById(int $id): ?array {
        $sql = "
            SELECT m.*, s.nom AS site_affectation
            FROM materiel m
            LEFT JOIN site s ON m.id_site_affectation = s.id_site
            WHERE m.id_materiel = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Crée un nouveau matériel
    public function createMateriel(array $data): bool {
        $sql = "INSERT INTO materiel (type_materiel, is_mobile, id_salle_fixe, id_site_affectation) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['type_materiel'],
            $data['is_mobile'],
            $data['id_salle_fixe'] ?? null,
            $data['id_site_affectation'] ?? null
        ]);
    }

    // Met à jour un matériel
    public function updateMateriel(int $id, array $data): bool {
        $sql = "UPDATE materiel SET type_materiel = ?, is_mobile = ?, id_salle_fixe = ?, id_site_affectation = ? WHERE id_materiel = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['type_materiel'],
            $data['is_mobile'],
            $data['id_salle_fixe'] ?? null,
            $data['id_site_affectation'] ?? null,
            $id
        ]);
    }

    // Supprime un matériel
    public function deleteMateriel(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM materiel WHERE id_materiel = ?");
        return $stmt->execute([$id]);
    }

    // Récupère le matériel fixe installé dans une salle donnée
    public function getMaterielBySalle(int $idSalle): array {
        $stmt = $this->pdo->prepare("SELECT * FROM materiel WHERE id_salle_fixe = ?");
        $stmt->execute([$idSalle]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    
    // Récupère les matériels mobiles.
    // Ici, nous renvoyons tous les matériels mobiles (is_mobile = 1) 
    // Mais dans un environnement idéal, vous pourriez lier un matériel mobile à un site via la colonne id_site_affectation.
    public function getMobileMaterielBySite(int $siteId): array {
        $sql = "SELECT * FROM materiel WHERE is_mobile = 1 AND id_site_affectation = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$siteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
