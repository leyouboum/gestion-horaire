<?php
declare(strict_types=1);

namespace app\Repositories;

use PDO;
use app\Config\Database;

class AuditLogRepository {
    protected PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Récupère tous les logs d’audit.
     *
     * @return array
     */
    public function getAllAuditLogs(): array {
        $stmt = $this->pdo->query("SELECT * FROM audit_log ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère un log d’audit par son ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getAuditLogById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM audit_log WHERE id_audit = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Crée un nouvel enregistrement d’audit.
     *
     * @param array $data
     * @return bool
     */
    public function createAuditLog(array $data): bool {
        $sql = "INSERT INTO audit_log (table_name, record_id, operation, message) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['table_name'],
            $data['record_id'] ?? null,
            $data['operation'],
            $data['message']
        ]);
    }
}
