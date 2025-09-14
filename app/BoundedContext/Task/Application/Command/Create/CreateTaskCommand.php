<?php

namespace App\BoundedContext\Task\Application\Command\Create;

use App\Common\Contract\Command;
use App\BoundedContext\Iam\Domain\Entity\User;
use App\BoundedContext\Task\Domain\Enum\TaskStatus;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CreateTaskCommand extends Command
{
    public function __construct(
        public readonly string     $title,
        public readonly string     $description,
        public readonly TaskStatus $status,
        public readonly User       $loggedUser
    ) {}
}
