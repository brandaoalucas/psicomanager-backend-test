<?php

namespace App\BoundedContext\AuditLog\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\AuditLog\Domain\Repository\AuditLogRepository;
use App\BoundedContext\AuditLog\Infrastructure\Inbound\Http\Requests\GetAuditLogsRequest;
use Illuminate\Http\JsonResponse;
use Spatie\RouteAttributes\Attributes\{Get, Prefix};

#[Prefix("/logs")]
readonly class AuditLogController
{
    public function __construct(
        private AuditLogRepository $auditLogRepository,
    ) {}

    #[Get("/")]
    public function index(GetAuditLogsRequest $request): JsonResponse
    {
        $auditLogs = $this->auditLogRepository->findRecent(30);
        
        return response()->json([
            'data' => array_map(fn($log) => $this->formatAuditLog($log), $auditLogs),
            'meta' => [
                'total' => count($auditLogs),
                'limit' => 30
            ]
        ]);
    }

    #[Get("/{id}")]
    public function show(string $id): JsonResponse
    {
        try {
            $auditLog = $this->auditLogRepository->findById(new \MongoDB\BSON\ObjectId($id));
            
            if (!$auditLog) {
                return response()->json(['error' => 'Log not found'], 404);
            }

            return response()->json([
                'data' => $this->formatAuditLog($auditLog)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid log ID'], 400);
        }
    }

    private function formatAuditLog($auditLog): array
    {
        return [
            'id' => (string) $auditLog->id,
            'user_id' => $auditLog->userId,
            'reference_type' => $auditLog->referenceType,
            'reference_id' => $auditLog->referenceId,
            'action' => $auditLog->action,
            'description' => $auditLog->description,
            'old_values' => $auditLog->oldValues,
            'new_values' => $auditLog->newValues,
            'ip_address' => $auditLog->ipAddress,
            'user_agent' => $auditLog->userAgent,
            'created_at' => $auditLog->createdAt?->toISOString(),
            'updated_at' => $auditLog->updatedAt?->toISOString(),
        ];
    }
}
