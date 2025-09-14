<?php

namespace App\BoundedContext\AuditLog\Application\Command\Create;

use App\BoundedContext\AuditLog\Domain\Entity\AuditLog;
use App\Helpers\Logger\Logger;

class CreateAuditLogCommandHandler
{
    public function handle(CreateAuditLogCommand $command): void
    {
        Logger::started(compact('command'));

        $fields = [
            'reference_type' => $command->referenceType,
            'reference_id'   => $command->referenceId,
            'user_id'        => $command->userId,
            'description'    => $command->description,
        ];

        if ($command->preventDuplicate) {
            if (AuditLog::query()->where($fields)->exists()) {
                return;
            }
        }

        AuditLog::create($fields);
    }
}
