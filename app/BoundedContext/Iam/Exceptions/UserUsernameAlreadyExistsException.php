<?php

namespace App\BoundedContext\Iam\Application\Exception;

use App\Exceptions\AbstractIApplicationException;

class UserUsernameAlreadyExistsException extends AbstractIApplicationException
{
    protected function message(): string
    {
        return "Já existe um usuário cadastrado com o username :username.";
    }
}
