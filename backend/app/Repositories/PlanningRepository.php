<?php
declare(strict_types=1);

namespace app\Repositories;

use PDO;
use PDOException;
use app\Config\Database;

class PlanningRepository {
    protected PDO $pdo;
    // Temps de déplacement minimum en secondes (1 heure = 3600 sec)
    private int $travelTime = 3600;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Récupère tous les plannings avec jointures pour retourner les noms et nouveaux champs.
     */
    public function getAll(): array {
        $sql = "
            SELECT 
                p.id_planning,
                p.id_salle,
                p.id_cours,
                p.id_groupe,
                p.date_heure_debut,
                p.date_heure_fin,
                p.id_annee,
                p.statut,
                s.id_site,
                s.nom_salle,
                st.nom AS site_name,
                c.nom_cours,
                g.nom_groupe,
                (SELECT GROUP_CONCAT(ma.type_materiel SEPARATOR ', ')
                 FROM materiel_affectation maff
                 JOIN materiel ma ON ma.id_materiel = maff.id_materiel
                 WHERE maff.id_planning = p.id_planning) AS materiels
            FROM planning p
            JOIN salle s ON s.id_salle = p.id_salle
            JOIN site st ON st.id_site = s.id_site
            JOIN cours c ON c.id_cours = p.id_cours
            JOIN groupe g ON g.id_groupe = p.id_groupe
            ORDER BY p.date_heure_debut DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Vérifie si un conflit existe pour la salle OU le groupe sur la période donnée.
     */
    private function hasConflict(array $data, ?int $excludeId = null): bool {
        $excludeId = $excludeId ?? 0;
        $sql = "
            SELECT COUNT(*) AS cnt
            FROM planning
            WHERE id_planning <> :excludeId
              AND (
                   id_salle  = :id_salle
                   OR id_groupe = :id_groupe
                  )
              AND (
                   date_heure_debut < :fin
                   AND date_heure_fin > :debut
                  )
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'excludeId' => $excludeId,
            'id_salle'  => $data['id_salle'],
            'id_groupe' => $data['id_groupe'],
            'debut'     => $data['date_heure_debut'],
            'fin'       => $data['date_heure_fin']
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && (int)$row['cnt'] > 0;
    }

    /**
     * Vérifie le temps de déplacement pour le groupe.
     * Vérifie que l'intervalle entre la séance précédente/suivante et la nouvelle est suffisant si les sites diffèrent.
     */
    private function hasTravelTimeConflict(array $data, ?int $excludeId = null): bool {
        // Récupérer l'ID du site associé à la salle du nouveau planning.
        $stmt = $this->pdo->prepare("SELECT id_site FROM salle WHERE id_salle = ?");
        $stmt->execute([$data['id_salle']]);
        $newSite = $stmt->fetchColumn();
        if (!$newSite) {
            return true; // Conflit si la salle n'est pas trouvée.
        }

        $newStart = strtotime($data['date_heure_debut']);
        $newEnd   = strtotime($data['date_heure_fin']);

        // Vérifier le planning précédent pour ce groupe.
        $sqlPrev = "
            SELECT s.id_site, p.date_heure_fin 
            FROM planning p
            JOIN salle s ON p.id_salle = s.id_salle
            WHERE p.id_groupe = :id_groupe
              AND p.date_heure_fin <= :newStart
        ";
        if ($excludeId) {
            $sqlPrev .= " AND p.id_planning <> :excludeId";
        }
        $sqlPrev .= " ORDER BY p.date_heure_fin DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sqlPrev);
        $params = [
            'id_groupe' => $data['id_groupe'],
            'newStart' => date('Y-m-d H:i:s', $newStart),
        ];
        if ($excludeId) {
            $params['excludeId'] = $excludeId;
        }
        $stmt->execute($params);
        $prev = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($prev) {
            if ($prev['id_site'] != $newSite) {
                $prevEnd = strtotime($prev['date_heure_fin']);
                if (($newStart - $prevEnd) < $this->travelTime) {
                    return true;
                }
            }
        }

        // Vérifier le planning suivant pour ce groupe.
        $sqlNext = "
            SELECT s.id_site, p.date_heure_debut 
            FROM planning p
            JOIN salle s ON p.id_salle = s.id_salle
            WHERE p.id_groupe = :id_groupe
              AND p.date_heure_debut >= :newEnd
        ";
        if ($excludeId) {
            $sqlNext .= " AND p.id_planning <> :excludeId";
        }
        $sqlNext .= " ORDER BY p.date_heure_debut ASC LIMIT 1";
        $stmt = $this->pdo->prepare($sqlNext);
        $params = [
            'id_groupe' => $data['id_groupe'],
            'newEnd' => date('Y-m-d H:i:s', $newEnd),
        ];
        if ($excludeId) {
            $params['excludeId'] = $excludeId;
        }
        $stmt->execute($params);
        $next = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($next) {
            if ($next['id_site'] != $newSite) {
                $nextStart = strtotime($next['date_heure_debut']);
                if (($nextStart - $newEnd) < $this->travelTime) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Récupère le planning d'un groupe, filtré par période (optionnel), avec jointures pour récupérer noms et matériels.
     */
    public function getPlanningByGroup(int $groupId, ?string $startDate = null, ?string $endDate = null): array {
        $sql = "
            SELECT 
                p.id_planning,
                p.id_salle,
                p.id_cours,
                p.id_groupe,
                p.date_heure_debut,
                p.date_heure_fin,
                p.id_annee,
                p.statut,
                c.nom_cours,
                s.nom_salle,
                st.nom AS site_name,
                g.nom_groupe,
                GROUP_CONCAT(DISTINCT m.type_materiel ORDER BY m.type_materiel SEPARATOR ', ') AS materiels_fixes,
                GROUP_CONCAT(DISTINCT mobile.type_materiel ORDER BY mobile.type_materiel SEPARATOR ', ') AS materiels_mobiles
            FROM planning p
            JOIN cours c ON c.id_cours = p.id_cours
            JOIN salle s ON s.id_salle = p.id_salle
            JOIN site st ON st.id_site = s.id_site
            JOIN groupe g ON g.id_groupe = p.id_groupe
            LEFT JOIN cours_materiel cm ON cm.id_cours = c.id_cours
            LEFT JOIN materiel m ON m.id_materiel = cm.id_materiel AND m.is_mobile = 0
            LEFT JOIN materiel_affectation ma ON ma.id_planning = p.id_planning
            LEFT JOIN materiel mobile ON mobile.id_materiel = ma.id_materiel AND mobile.is_mobile = 1
            WHERE p.id_groupe = ?
        ";
        $params = [$groupId];
        if ($startDate && $endDate) {
            $sql .= " AND p.date_heure_debut BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        } elseif ($startDate) {
            $sql .= " AND p.date_heure_debut >= ?";
            $params[] = $startDate;
        } elseif ($endDate) {
            $sql .= " AND p.date_heure_debut <= ?";
            $params[] = $endDate;
        }
        $sql .= "
            GROUP BY 
                p.id_planning, p.id_salle, p.id_cours, p.id_groupe,
                p.date_heure_debut, p.date_heure_fin, p.id_annee, p.statut,
                c.nom_cours, s.nom_salle, st.nom, g.nom_groupe
            ORDER BY p.date_heure_debut ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Nouvelle méthode : Récupère le planning d'un groupe pour une année académique spécifique,
     * avec les mêmes jointures et options de filtrage.
     */
    public function getPlanningByGroupAndAnnee(int $groupId, int $anneeId, ?string $startDate = null, ?string $endDate = null): array {
        $sql = "
            SELECT 
                p.id_planning,
                p.id_salle,
                p.id_cours,
                p.id_groupe,
                p.date_heure_debut,
                p.date_heure_fin,
                p.id_annee,
                p.statut,
                c.nom_cours,
                s.nom_salle,
                st.nom AS site_name,
                g.nom_groupe,
                GROUP_CONCAT(DISTINCT m.type_materiel ORDER BY m.type_materiel SEPARATOR ', ') AS materiels_fixes,
                GROUP_CONCAT(DISTINCT mobile.type_materiel ORDER BY mobile.type_materiel SEPARATOR ', ') AS materiels_mobiles
            FROM planning p
            JOIN cours c ON c.id_cours = p.id_cours
            JOIN salle s ON s.id_salle = p.id_salle
            JOIN site st ON st.id_site = s.id_site
            JOIN groupe g ON g.id_groupe = p.id_groupe
            LEFT JOIN cours_materiel cm ON cm.id_cours = c.id_cours
            LEFT JOIN materiel m ON m.id_materiel = cm.id_materiel AND m.is_mobile = 0
            LEFT JOIN materiel_affectation ma ON ma.id_planning = p.id_planning
            LEFT JOIN materiel mobile ON mobile.id_materiel = ma.id_materiel AND mobile.is_mobile = 1
            WHERE p.id_groupe = ? AND p.id_annee = ?
        ";
        $params = [$groupId, $anneeId];
        if ($startDate && $endDate) {
            $sql .= " AND p.date_heure_debut BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        } elseif ($startDate) {
            $sql .= " AND p.date_heure_debut >= ?";
            $params[] = $startDate;
        } elseif ($endDate) {
            $sql .= " AND p.date_heure_debut <= ?";
            $params[] = $endDate;
        }
        $sql .= "
            GROUP BY 
                p.id_planning, p.id_salle, p.id_cours, p.id_groupe,
                p.date_heure_debut, p.date_heure_fin, p.id_annee, p.statut,
                c.nom_cours, s.nom_salle, st.nom, g.nom_groupe
            ORDER BY p.date_heure_debut ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère un planning via son ID.
     */
    public function getPlanningById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM planning WHERE id_planning = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Crée un nouveau planning avec vérification des conflits et affectation du matériel mobile.
     * Expects $data avec :
     * - id_salle, id_cours, id_groupe, date_heure_debut, date_heure_fin, id_annee,
     * - facultativement statut (sinon 'planifie'),
     * - facultativement materiel_mobile (tableau d'IDs de matériel mobile)
     */
    public function createPlanning(array $data): bool {
        if ($this->hasConflict($data)) {
            return false;
        }
        if ($this->hasTravelTimeConflict($data)) {
            return false;
        }
        $statut = $data['statut'] ?? 'planifie';
        $sql = "
            INSERT INTO planning (id_salle, id_cours, id_groupe, date_heure_debut, date_heure_fin, id_annee, statut)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute([
            $data['id_salle'],
            $data['id_cours'],
            $data['id_groupe'],
            $data['date_heure_debut'],
            $data['date_heure_fin'],
            $data['id_annee'],
            $statut
        ]);
        if (!$res) {
            return false;
        }
        $planningId = (int)$this->pdo->lastInsertId();

        if (isset($data['materiel_mobile']) && is_array($data['materiel_mobile'])) {
            $sqlAffect = "INSERT INTO materiel_affectation (id_materiel, id_planning, date_heure_debut, date_heure_fin) VALUES (?, ?, ?, ?)";
            $stmtAffect = $this->pdo->prepare($sqlAffect);
            foreach ($data['materiel_mobile'] as $id_materiel) {
                $stmtAffect->execute([
                    $id_materiel,
                    $planningId,
                    $data['date_heure_debut'],
                    $data['date_heure_fin']
                ]);
            }
        }
        return true;
    }

    /**
     * Met à jour un planning avec vérification des conflits et mise à jour de l'affectation du matériel mobile.
     */
    public function updatePlanning(int $id, array $data): bool {
        if ($this->hasConflict($data, $id)) {
            return false;
        }
        if ($this->hasTravelTimeConflict($data, $id)) {
            return false;
        }
        $statut = $data['statut'] ?? 'planifie';
        $sql = "
            UPDATE planning 
            SET id_salle = ?, id_cours = ?, id_groupe = ?, date_heure_debut = ?, date_heure_fin = ?, id_annee = ?, statut = ?
            WHERE id_planning = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute([
            $data['id_salle'],
            $data['id_cours'],
            $data['id_groupe'],
            $data['date_heure_debut'],
            $data['date_heure_fin'],
            $data['id_annee'],
            $statut,
            $id
        ]);
        if (!$res) {
            return false;
        }
        $stmtDel = $this->pdo->prepare("DELETE FROM materiel_affectation WHERE id_planning = ?");
        $stmtDel->execute([$id]);
        if (isset($data['materiel_mobile']) && is_array($data['materiel_mobile'])) {
            $sqlAffect = "INSERT INTO materiel_affectation (id_materiel, id_planning, date_heure_debut, date_heure_fin) VALUES (?, ?, ?, ?)";
            $stmtAffect = $this->pdo->prepare($sqlAffect);
            foreach ($data['materiel_mobile'] as $id_materiel) {
                $stmtAffect->execute([
                    $id_materiel,
                    $id,
                    $data['date_heure_debut'],
                    $data['date_heure_fin']
                ]);
            }
        }
        return true;
    }

    /**
     * Supprime un planning et les affectations de matériel mobile associées.
     */
    public function deletePlanning(int $id): bool {
        $stmtDel = $this->pdo->prepare("DELETE FROM materiel_affectation WHERE id_planning = ?");
        $stmtDel->execute([$id]);
        $stmt = $this->pdo->prepare("DELETE FROM planning WHERE id_planning = ?");
        return $stmt->execute([$id]);
    }
}
