<?php

namespace App\BoundedContext\Task\Application\Command\Restart;

use App\BoundedContext\Iam\Domain\Entity\User;
use App\Common\Contract\Command;

class RestartTaskCommand extends Command
{
    public function __construct(
        public readonly int $taskId,
        public readonly User $loggedUser,
    ) {}
}
