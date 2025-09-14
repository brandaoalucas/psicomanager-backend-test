<?php

namespace App\BoundedContext\Task\Application\Query\GetTaskById;

use App\BoundedContext\Iam\Domain\Entity\User;
use App\Common\Contract\Query;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class GetTaskByIdQuery extends Query
{
    public function __construct(
        public readonly int $taskId,
        public readonly User $loggedUser,
    ) {}
}
