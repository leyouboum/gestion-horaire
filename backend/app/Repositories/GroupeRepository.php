<?php
declare(strict_types=1);

namespace app\Repositories;

use PDO;
use app\Config\Database;

class GroupeRepository {
    protected PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Vérifie si un groupe portant le même nom existe déjà pour l'université donnée.
     * Optionnellement, on peut exclure un groupe en cours d'édition.
     */
    private function existsGroupInUniversity(string $nom_groupe, int $univId, ?int $excludeId = null): bool {
        $sql = "
            SELECT COUNT(*) 
            FROM groupe g
            JOIN groupe_site gs ON g.id_groupe = gs.id_groupe
            JOIN site s ON gs.id_site = s.id_site
            WHERE s.id_universite = :univId
              AND g.nom_groupe = :nom_groupe
        ";
        if ($excludeId !== null) {
            $sql .= " AND g.id_groupe != :excludeId";
        }
        $stmt = $this->pdo->prepare($sql);
        $params = [
            'univId' => $univId,
            'nom_groupe' => $nom_groupe
        ];
        if ($excludeId !== null) {
            $params['excludeId'] = $excludeId;
        }
        $stmt->execute($params);
        return ((int)$stmt->fetchColumn()) > 0;
    }

    /**
     * Récupère tous les groupes avec leurs sites associés.
     */
    public function getAll(): array {
        $sql = "SELECT * FROM groupe ORDER BY id_groupe ASC";
        $stmt = $this->pdo->query($sql);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($groups as &$g) {
            $g['sites'] = $this->getSitesForGroupe((int)$g['id_groupe']);
        }
        return $groups;
    }

    /**
     * Récupère un groupe par son ID avec ses associations.
     */
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM groupe WHERE id_groupe = ?");
        $stmt->execute([$id]);
        $g = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$g) {
            return null;
        }
        $g['sites'] = $this->getSitesForGroupe($id);
        return $g;
    }

    /**
     * Crée un groupe et enregistre ses associations de sites.
     * La vérification d'unicité se fait sur le nom du groupe pour l'université.
     * On attend dans $data une clé 'id_universite' indiquant l'université à laquelle le groupe doit appartenir.
     */
    public function create(array $data): bool {
        // Vérification de l'unicité du nom pour l'université
        if ($this->existsGroupInUniversity($data['nom_groupe'], (int)$data['id_universite'])) {
            return false;
        }
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("INSERT INTO groupe (nom_groupe, nb_etudiants) VALUES (?, ?)");
            $stmt->execute([
                $data['nom_groupe'],
                $data['nb_etudiants']
            ]);
            $newId = (int)$this->pdo->lastInsertId();

            if (!empty($data['site_principal'])) {
                $this->insertGroupeSite($newId, (int)$data['site_principal'], true);
            }
            if (!empty($data['sites_secondaires']) && is_array($data['sites_secondaires'])) {
                foreach ($data['sites_secondaires'] as $siteId) {
                    if ((int)$siteId === (int)($data['site_principal'] ?? 0)) {
                        continue;
                    }
                    $this->insertGroupeSite($newId, (int)$siteId, false);
                }
            }
            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Met à jour un groupe et ses associations de sites.
     * La vérification d'unicité s'effectue également, en excluant le groupe mis à jour.
     */
    public function update(int $id, array $data): bool {
        // Vérification de l'unicité du nom pour l'université (excluant le groupe en cours)
        if ($this->existsGroupInUniversity($data['nom_groupe'], (int)$data['id_universite'], $id)) {
            return false;
        }
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("UPDATE groupe SET nom_groupe = ?, nb_etudiants = ? WHERE id_groupe = ?");
            $stmt->execute([
                $data['nom_groupe'],
                $data['nb_etudiants'],
                $id
            ]);

            $del = $this->pdo->prepare("DELETE FROM groupe_site WHERE id_groupe = ?");
            $del->execute([$id]);

            if (!empty($data['site_principal'])) {
                $this->insertGroupeSite($id, (int)$data['site_principal'], true);
            }
            if (!empty($data['sites_secondaires']) && is_array($data['sites_secondaires'])) {
                foreach ($data['sites_secondaires'] as $siteId) {
                    if ((int)$siteId === (int)($data['site_principal'] ?? 0)) {
                        continue;
                    }
                    $this->insertGroupeSite($id, (int)$siteId, false);
                }
            }
            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Supprime un groupe.
     */
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM groupe WHERE id_groupe = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Insère une association dans la table groupe_site.
     */
    private function insertGroupeSite(int $idGroupe, int $idSite, bool $isPrincipal): void {
        $stmt = $this->pdo->prepare("INSERT INTO groupe_site (id_groupe, id_site, is_principal) VALUES (?, ?, ?)");
        $stmt->execute([$idGroupe, $idSite, $isPrincipal]);
    }

    /**
     * Récupère la liste des sites associés à un groupe.
     */
    private function getSitesForGroupe(int $idGroupe): array {
        $sql = "
            SELECT 
                s.id_site, 
                s.nom AS nom_site, 
                s.id_universite,
                univ.nom AS nom_universite,
                gs.is_principal
            FROM groupe_site gs
            JOIN site s ON gs.id_site = s.id_site
            JOIN universite univ ON s.id_universite = univ.id_universite
            WHERE gs.id_groupe = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idGroupe]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère les groupes associés à un site.
     */
    public function getGroupesBySite(int $idSite): array {
        $sql = "
            SELECT g.*
            FROM groupe g
            JOIN groupe_site gs ON g.id_groupe = gs.id_groupe
            WHERE gs.id_site = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idSite]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        foreach ($groups as &$g) {
            $g['sites'] = $this->getSitesForGroupe((int)$g['id_groupe']);
        }
        return $groups;
    }

    /**
     * Récupère les groupes d'une université.
     */
    public function getGroupsByUniversite(int $universiteId): array {
        $sql = "
            SELECT DISTINCT g.*
            FROM groupe g
            JOIN groupe_site gs ON g.id_groupe = gs.id_groupe
            JOIN site s ON gs.id_site = s.id_site
            WHERE s.id_universite = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$universiteId]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        foreach ($groups as &$g) {
            $g['sites'] = $this->getSitesForGroupe((int)$g['id_groupe']);
        }
        return $groups;
    }
}
