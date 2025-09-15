<?php

namespace App\BoundedContext\AuditLog\Application\Query\GetPaginatedAuditLogs;

use App\Common\Enum\ReferenceType;
use App\Common\Contract\Query;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class GetPaginatedAuditLogsQuery extends Query
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $userName,
        public readonly ?ReferenceType $referenceType,
        public readonly ?int $referenceId,
    ) {}
}
