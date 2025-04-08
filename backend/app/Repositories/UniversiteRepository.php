<?php
declare(strict_types=1);

namespace app\Repositories;

use PDO;
use PDOException;
use app\Config\Database;

class UniversiteRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Récupère toutes les universités.
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM universite ORDER BY nom DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère une université par son ID.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM universite WHERE id_universite = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Crée une nouvelle université.
     * Retourne false en cas de doublon (erreur 1062) ou d'autre problème.
     */
    public function create(array $data): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO universite (nom) VALUES (?)");
            return $stmt->execute([$data['nom']]);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                // Doublon détecté
                return false;
            }
            throw $e;
        }
    }

    /**
     * Met à jour une université existante.
     */
    public function update(int $id, array $data): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE universite SET nom = ? WHERE id_universite = ?");
            return $stmt->execute([$data['nom'], $id]);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                // Doublon détecté lors de la mise à jour
                return false;
            }
            throw $e;
        }
    }

    /**
     * Supprime une université.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM universite WHERE id_universite = ?");
        return $stmt->execute([$id]);
    }
}
