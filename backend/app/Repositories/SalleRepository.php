<?php
declare(strict_types=1);

namespace app\Repositories;

use PDO;
use app\Config\Database;

class SalleRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    // Récupère toutes les salles avec le nom du site associé
    public function getAllSalles(): array {
        $sql = "
            SELECT s.id_salle, s.nom_salle, s.capacite_max, si.nom AS nom_site, si.id_site
            FROM salle s
            JOIN site si ON s.id_site = si.id_site
            ORDER BY s.id_salle DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Récupère une salle par son ID
    public function getSalleById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM salle WHERE id_salle = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Crée une nouvelle salle
    public function createSalle(array $data): bool {
        $stmt = $this->pdo->prepare("INSERT INTO salle (id_site, nom_salle, capacite_max) VALUES (?, ?, ?)");
        return $stmt->execute([
            $data['id_site'],
            $data['nom_salle'],
            $data['capacite_max']
        ]);
    }

    // Met à jour une salle existante
    public function updateSalle(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("UPDATE salle SET id_site = ?, nom_salle = ?, capacite_max = ? WHERE id_salle = ?");
        return $stmt->execute([
            $data['id_site'],
            $data['nom_salle'],
            $data['capacite_max'],
            $id
        ]);
    }

    // Supprime une salle
    public function deleteSalle(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM salle WHERE id_salle = ?");
        return $stmt->execute([$id]);
    }

    public function getBySite(int $idSite): array {
        $sql = "SELECT * FROM salle WHERE id_site = :idSite";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idSite' => $idSite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Récupère les salles associées à un groupe (via la table planning)
    public function getSallesByGroup(int $groupId): array {
        $sql = "
            SELECT DISTINCT s.*
            FROM salle s
            JOIN planning p ON s.id_salle = p.id_salle
            WHERE p.id_groupe = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$groupId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
