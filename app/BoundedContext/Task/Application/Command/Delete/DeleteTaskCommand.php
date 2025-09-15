<?php

namespace App\BoundedContext\Task\Application\Command\Delete;

use App\BoundedContext\Iam\Domain\Entity\User;
use App\Common\Contract\Command;

class DeleteTaskCommand extends Command
{
    public function __construct(
        public readonly int $taskId,
        public readonly User $loggedUser,
    ) {}
}
