<?php
declare(strict_types=1);

namespace app\Models;

class AuditLog implements \JsonSerializable {
    private ?int $id_audit;
    private string $table_name;
    private ?int $record_id;
    private string $operation;
    private string $message;
    private \DateTime $created_at;

    public function __construct(
        ?int $id_audit,
        string $table_name,
        ?int $record_id,
        string $operation,
        string $message,
        \DateTime $created_at
    ) {
        $this->id_audit    = $id_audit;
        $this->table_name  = $table_name;
        $this->record_id   = $record_id;
        $this->operation   = $operation;
        $this->message     = $message;
        $this->created_at  = $created_at;
    }

    public function getIdAudit(): ?int {
        return $this->id_audit;
    }

    public function getTableName(): string {
        return $this->table_name;
    }

    public function getRecordId(): ?int {
        return $this->record_id;
    }

    public function getOperation(): string {
        return $this->operation;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getCreatedAt(): \DateTime {
        return $this->created_at;
    }

    public function setTableName(string $table_name): void {
        $this->table_name = $table_name;
    }

    public function setRecordId(?int $record_id): void {
        $this->record_id = $record_id;
    }

    public function setOperation(string $operation): void {
        $this->operation = $operation;
    }

    public function setMessage(string $message): void {
        $this->message = $message;
    }

    public function setCreatedAt(\DateTime $created_at): void {
        $this->created_at = $created_at;
    }

    public function jsonSerialize(): array {
        return [
            'id_audit'   => $this->id_audit,
            'table_name' => $this->table_name,
            'record_id'  => $this->record_id,
            'operation'  => $this->operation,
            'message'    => $this->message,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
