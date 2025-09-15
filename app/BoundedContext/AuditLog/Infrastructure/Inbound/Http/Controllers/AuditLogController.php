<?php

namespace App\BoundedContext\AuditLog\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\AuditLog\Application\Query\GetPaginatedAuditLogs\GetPaginatedAuditLogsQueryHandler;
use App\BoundedContext\AuditLog\Infrastructure\Inbound\Http\Requests\GetPaginatedAuditLogsRequest;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Illuminate\Pagination\LengthAwarePaginator;

#[Prefix("/audit-logs")]
#[Middleware("auth:sanctum")]
readonly class AuditLogController
{
    public function __construct(
        private GetPaginatedAuditLogsQueryHandler $getPaginatedAuditLogsQueryHandler,
    ) {}

    #[Get("/")]
    public function paginate(GetPaginatedAuditLogsRequest $request): LengthAwarePaginator
    {
        return $this->getPaginatedAuditLogsQueryHandler->handle($request->toQuery());
    }
}
