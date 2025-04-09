<?php
declare(strict_types=1);

namespace app\Repositories;

use PDO;
use PDOException;
use app\Config\Database;

class AnneeAcademiqueRepository {
    protected PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Récupère toutes les années académiques.
     *
     * @return array
     */
    public function getAll(): array {
        $sql = "SELECT * FROM annee_academique ORDER BY date_debut DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère une année académique par son ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM annee_academique WHERE id_annee = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Crée une nouvelle année académique.
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO annee_academique (libelle, date_debut, date_fin) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['libelle'],
            $data['date_debut'], // Format attendu : 'YYYY-MM-DD'
            $data['date_fin']    // Format attendu : 'YYYY-MM-DD'
        ]);
    }

    /**
     * Met à jour une année académique existante.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE annee_academique SET libelle = ?, date_debut = ?, date_fin = ? WHERE id_annee = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['libelle'],
            $data['date_debut'],
            $data['date_fin'],
            $id
        ]);
    }

    /**
     * Supprime une année académique.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM annee_academique WHERE id_annee = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Gérer les erreurs de contrainte d'intégrité (ex : si des plannings font référence à cette année)
            return false;
        }
    }
}
