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
     * 
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
     * 
     *
     * @param int $id
     * @return array|null
     */
    public function getAuditLog(int $id): ?array {
        $log = $this->auditService->getAuditLogById($id);
        return $log ? $log->jsonSerialize() : null;
    }
}
