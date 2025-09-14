<?php

namespace App\BoundedContext\AuditLog\Infrastructure\Outbound\MongoDB;

use App\BoundedContext\AuditLog\Domain\Entity\AuditLog;
use App\BoundedContext\AuditLog\Domain\Repository\AuditLogRepository;
use MongoDB\BSON\ObjectId;
use MongoDB\Client;
use MongoDB\Database;
use MongoDB\Collection;

class MongoAuditLogRepository implements AuditLogRepository
{
    private Database $database;
    private Collection $collection;

    public function __construct(Client $mongoClient)
    {
        $this->database = $mongoClient->selectDatabase(config('database.mongodb.database'));
        $this->collection = $this->database->selectCollection('audit_logs');
    }

    public function create(AuditLog $auditLog): AuditLog
    {
        $data = $auditLog->toArray();
        $result = $this->collection->insertOne($data);
        
        return new AuditLog(
            id: $result->getInsertedId(),
            userId: $auditLog->userId,
            referenceType: $auditLog->referenceType,
            referenceId: $auditLog->referenceId,
            action: $auditLog->action,
            description: $auditLog->description,
            oldValues: $auditLog->oldValues,
            newValues: $auditLog->newValues,
            ipAddress: $auditLog->ipAddress,
            userAgent: $auditLog->userAgent,
            createdAt: $auditLog->createdAt,
            updatedAt: $auditLog->updatedAt,
        );
    }

    public function findById(ObjectId $id): ?AuditLog
    {
        $document = $this->collection->findOne(['_id' => $id]);
        
        if (!$document) {
            return null;
        }

        return $this->documentToAuditLog($document);
    }

    public function findByReference(string $referenceType, int $referenceId): array
    {
        $cursor = $this->collection->find(
            [
                'reference_type' => $referenceType,
                'reference_id' => $referenceId
            ],
            ['sort' => ['created_at' => -1]]
        );

        return $this->cursorToAuditLogs($cursor);
    }

    public function findByUser(int $userId, int $limit = 30): array
    {
        $cursor = $this->collection->find(
            ['user_id' => $userId],
            [
                'sort' => ['created_at' => -1],
                'limit' => $limit
            ]
        );

        return $this->cursorToAuditLogs($cursor);
    }

    public function findRecent(int $limit = 30): array
    {
        $cursor = $this->collection->find(
            [],
            [
                'sort' => ['created_at' => -1],
                'limit' => $limit
            ]
        );

        return $this->cursorToAuditLogs($cursor);
    }

    public function findByAction(string $action, int $limit = 30): array
    {
        $cursor = $this->collection->find(
            ['action' => $action],
            [
                'sort' => ['created_at' => -1],
                'limit' => $limit
            ]
        );

        return $this->cursorToAuditLogs($cursor);
    }

    private function documentToAuditLog($document): AuditLog
    {
        return new AuditLog(
            id: $document['_id'],
            userId: $document['user_id'],
            referenceType: $document['reference_type'],
            referenceId: $document['reference_id'],
            action: $document['action'],
            description: $document['description'],
            oldValues: $document['old_values'] ?? null,
            newValues: $document['new_values'] ?? null,
            ipAddress: $document['ip_address'] ?? null,
            userAgent: $document['user_agent'] ?? null,
            createdAt: $document['created_at'] ? \Carbon\CarbonImmutable::instance($document['created_at']) : null,
            updatedAt: $document['updated_at'] ? \Carbon\CarbonImmutable::instance($document['updated_at']) : null,
        );
    }

    private function cursorToAuditLogs($cursor): array
    {
        $auditLogs = [];
        foreach ($cursor as $document) {
            $auditLogs[] = $this->documentToAuditLog($document);
        }
        return $auditLogs;
    }
}
