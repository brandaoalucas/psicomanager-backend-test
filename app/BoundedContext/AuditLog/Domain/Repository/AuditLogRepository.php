<?php

namespace App\BoundedContext\AuditLog\Domain\Repository;

use App\BoundedContext\AuditLog\Domain\Entity\AuditLog;
use MongoDB\BSON\ObjectId;

interface AuditLogRepository
{
    public function create(AuditLog $auditLog): AuditLog;
    
    public function findById(ObjectId $id): ?AuditLog;
    
    public function findByReference(string $referenceType, int $referenceId): array;
    
    public function findByUser(int $userId, int $limit = 30): array;
    
    public function findRecent(int $limit = 30): array;
    
    public function findByAction(string $action, int $limit = 30): array;
}
