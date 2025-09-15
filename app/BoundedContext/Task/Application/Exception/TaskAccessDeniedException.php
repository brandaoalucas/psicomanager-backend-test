<?php

namespace App\BoundedContext\Task\Application\Exception;

use App\Exceptions\AbstractIApplicationException;

class TaskAccessDeniedException extends AbstractIApplicationException
{
    protected function message(): string
    {
        return "Task não encontrada para o ID passado.";
    }
}
