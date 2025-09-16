<?php

namespace App\BoundedContext\AuditLog\Application\Query\GetPaginatedAuditLogs;

use App\BoundedContext\AuditLog\Domain\Entity\AuditLog;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class GetPaginatedAuditLogsQueryHandler
{
    public function __construct()
    {
        //Unused
    }

    public function handle(GetPaginatedAuditLogsQuery $query): LengthAwarePaginator
    {
        $auditLogsQuery = AuditLog::query();

        if ($query->id) {
            $auditLogsQuery->where('_id', $query->id);
        }

        if ($query->userId) {
            $auditLogsQuery->where('user_id', $query->userId);
        }

        if ($query->referenceType) {
            $auditLogsQuery->where('reference_type', $query->referenceType);
        }

        if ($query->referenceId) {
            $auditLogsQuery->where('reference_id', $query->referenceId);
        }

        if ($query->userName) {
            $auditLogsQuery->where('user_name', 'like', "%{$query->userName}%");
        }

        if ($query->action) {
            $auditLogsQuery->where('action', $query->action);
        }

        return $auditLogsQuery
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }
}
