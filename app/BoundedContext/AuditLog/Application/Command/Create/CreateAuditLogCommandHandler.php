<?php

namespace App\BoundedContext\AuditLog\Application\Command\Create;

use App\BoundedContext\AuditLog\Domain\Entity\AuditLog;
use App\BoundedContext\AuditLog\Application\Exceptions\AuditLogAlreadyExistsException;
use App\Helpers\Logger\Logger;
use Illuminate\Support\Facades\DB;

class CreateAuditLogCommandHandler
{
    public function handle(CreateAuditLogCommand $command): AuditLog
    {
        Logger::started(compact('command'));

        $fields = $this->validate($command);

        return DB::transaction(fn() => $this->create($fields));
    }

    private function validate(CreateAuditLogCommand $command): array
    {
        $fields = $this->toCreateFields($command);

        if ($command->preventDuplicate) {
            $this->validatePreventDuplicate($fields);
        }

        return $fields;
    }

    private function validatePreventDuplicate(array $fields): void
    {
        if (AuditLog::query()->where($fields)->exists()) {
            throw new AuditLogAlreadyExistsException();
        }
    }

    private function toCreateFields(CreateAuditLogCommand $command): array
    {
        return [
            'reference_type' => $command->referenceType,
            'reference_id'   => $command->referenceId,
            'user_id'        => $command->userId,
            'description'    => $command->description,
        ];
    }

    private function create(array $fields): AuditLog
    {
        return AuditLog::create($fields);
    }
}
