<?php

namespace App\BoundedContext\AuditLog\Application\Exceptions;

use App\Exceptions\AbstractIApplicationException;

class AuditLogAlreadyExistsException extends AbstractIApplicationException
{
    protected function message(): string
    {
        return "Já existe um registro de auditoria com os dados informados.";
    }

    public function getErrorCode(): string
    {
        return 'AUDIT_LOG_ALREADY_EXISTS';
    }

    public function getHttpCode(): int
    {
        return 409;
    }
}
