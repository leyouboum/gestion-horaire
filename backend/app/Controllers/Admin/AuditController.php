<?php
declare(strict_types=1);

namespace app\Controllers\Admin;

use app\Services\AuditService;

class AuditController {
    protected AuditService $auditService;

    public function __construct() {
        $this->auditService = new AuditService();
    }

    /**
     * Retourne tous les logs d’audit.
     *
     * @return array
     */
    public function listAuditLogs(): array {
        $logs = $this->auditService->getAllAuditLogs();
        $result = [];
        foreach ($logs as $log) {
            $result[] = $log->jsonSerialize();
        }
        return $result;
    }

    /**
     * Retourne un log d’audit par son ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getAuditLog(int $id): ?array {
        $log = $this->auditService->getAuditLogById($id);
        return $log ? $log->jsonSerialize() : null;
    }
}
