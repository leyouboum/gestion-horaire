<?php
declare(strict_types=1);

namespace app\Repositories;

use PDO;
use app\Config\Database;

class SiteRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    // Récupère tous les sites avec le nom de l'université
    public function getAllSites(): array
    {
        $sql = "
            SELECT 
                s.id_site,
                s.id_universite,
                s.nom,
                s.heure_ouverture,
                s.heure_fermeture,
                u.nom AS nom_universite
            FROM site s
            JOIN universite u ON s.id_universite = u.id_universite
            ORDER BY s.id_site DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Récupère un site par son ID (avec le nom de l'université)
    public function getSiteById(int $id): ?array
    {
        $sql = "
            SELECT 
                s.id_site,
                s.id_universite,
                s.nom,
                s.heure_ouverture,
                s.heure_fermeture,
                u.nom AS nom_universite
            FROM site s
            JOIN universite u ON s.id_universite = u.id_universite
            WHERE s.id_site = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Crée un nouveau site
    public function createSite(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO site (id_universite, nom, heure_ouverture, heure_fermeture)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['id_universite'],
            $data['nom'],
            $data['heure_ouverture'],
            $data['heure_fermeture']
        ]);
    }

    // Met à jour un site
    public function updateSite(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE site
            SET id_universite = ?,
                nom = ?,
                heure_ouverture = ?,
                heure_fermeture = ?
            WHERE id_site = ?
        ");
        return $stmt->execute([
            $data['id_universite'],
            $data['nom'],
            $data['heure_ouverture'],
            $data['heure_fermeture'],
            $id
        ]);
    }

    // Supprime un site
    public function deleteSite(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM site WHERE id_site = ?");
        return $stmt->execute([$id]);
    }

    // Récupère les sites d'une université donnée
    public function getByUniversite(int $univId): array
    {
        $sql = "SELECT * FROM site WHERE id_universite = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$univId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Récupère les sites associés à un groupe (optionnel)
    public function getSitesByGroup(int $groupId): array
    {
        $sql = "
            SELECT DISTINCT st.*
            FROM site st
            JOIN salle s ON st.id_site = s.id_site
            JOIN planning p ON s.id_salle = p.id_salle
            WHERE p.id_groupe = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$groupId]);
        $sitesFromPlanning = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $sql2 = "
            SELECT st.*
            FROM site st
            JOIN groupe_site gs ON st.id_site = gs.id_site
            WHERE gs.id_groupe = ?
        ";
        $stmt2 = $this->pdo->prepare($sql2);
        $stmt2->execute([$groupId]);
        $sitesFromGroupe = $stmt2->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Fusion et suppression des doublons
        $allSites = array_merge($sitesFromPlanning, $sitesFromGroupe);
        $unique = [];
        $siteIds = [];
        foreach ($allSites as $site) {
            if (!in_array($site['id_site'], $siteIds, true)) {
                $unique[] = $site;
                $siteIds[] = $site['id_site'];
            }
        }
        return $unique;
    }
}
