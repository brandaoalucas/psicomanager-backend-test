<?php

namespace App\BoundedContext\Iam\Application\Exception;

use App\Exceptions\AbstractIApplicationException;

class UserEmailAlreadyExistsException extends AbstractIApplicationException
{
    protected function message(): string
    {
        return "Já existe um usuário cadastrado com o email :email.";
    }

    public function getErrorCode(): string
    {
        return 'USER_EMAIL_EXISTS';
    }

    public function getHttpCode(): int
    {
        return 422;
    }
}
