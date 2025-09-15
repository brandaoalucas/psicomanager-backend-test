<?php

namespace App\BoundedContext\AuditLog\Application\Command\Create;

use App\Common\Enum\ReferenceType;
use App\Common\Contract\Command;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CreateAuditLogCommand extends Command
{
    public function __construct(
        public readonly int $userId,
        public readonly int $referenceId,
        public readonly ReferenceType $referenceType,
        public readonly string $description,
        public readonly bool $preventDuplicate = false,
    ) {}
}
