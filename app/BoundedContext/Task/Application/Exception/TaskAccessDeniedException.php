<?php

namespace App\BoundedContext\Task\Application\Exception;

use App\Common\Exception\BaseApplicationException;

class TaskAccessDeniedException extends BaseApplicationException
{
    protected $message = 'Access denied to this task';

    public function getLogLevel(): string
    {
        return 'warning';
    }
}
