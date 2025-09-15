<?php

namespace App\BoundedContext\Task\Application\Query\GetPaginatedTasks;

use App\BoundedContext\Iam\Domain\Entity\User;

class GetPaginatedTasksQuery
{
    public function __construct(
        public readonly User $loggedUser,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $status = null,
        public readonly ?string $search = null,
        public readonly string $sortBy = 'created_at',
        public readonly string $sortOrder = 'desc',
    ) {}
}
