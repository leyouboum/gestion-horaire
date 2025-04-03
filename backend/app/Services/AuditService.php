<?php
declare(strict_types=1);

namespace app\Services;

use app\Repositories\AuditLogRepository;
use app\Models\AuditLog;

class AuditService {
    protected AuditLogRepository $auditRepo;

    public function __construct() {
        $this->auditRepo = new AuditLogRepository();
    }

    /**
     * Enregistre une opération d’audit.
     *
     * @param string $tableName
     * @param int|null $recordId
     * @param string $operation
     * @param string $message
     * @return bool
     */
    public function logOperation(string $tableName, ?int $recordId, string $operation, string $message): bool {
        $data = [
            'table_name' => $tableName,
            'record_id'  => $recordId,
            'operation'  => $operation,
            'message'    => $message
        ];
        return $this->auditRepo->createAuditLog($data);
    }

    /**
     * Récupère tous les logs d’audit sous forme d’objets AuditLog.
     *
     * @return AuditLog[]
     */
    public function getAllAuditLogs(): array {
        $data = $this->auditRepo->getAllAuditLogs();
        $logs = [];
        foreach ($data as $row) {
            $logs[] = new AuditLog(
                (int)$row['id_audit'],
                $row['table_name'],
                isset($row['record_id']) ? (int)$row['record_id'] : null,
                $row['operation'],
                $row['message'],
                new \DateTime($row['created_at'])
            );
        }
        return $logs;
    }

    /**
     * Récupère un log d’audit par son ID sous forme d’objet AuditLog.
     *
     * @param int $id
     * @return AuditLog|null
     */
    public function getAuditLogById(int $id): ?AuditLog {
        $row = $this->auditRepo->getAuditLogById($id);
        if (!$row) {
            return null;
        }
        return new AuditLog(
            (int)$row['id_audit'],
            $row['table_name'],
            isset($row['record_id']) ? (int)$row['record_id'] : null,
            $row['operation'],
            $row['message'],
            new \DateTime($row['created_at'])
        );
    }
}
