<?php

namespace App\BoundedContext\Task\Application\Command\UpdateStatus;

use TaskStatus;

class UpdateStatusTaskCommand
{
    public function __construct(
        public int $id,
        public TaskStatus $status,
    ) {}
}
