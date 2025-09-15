<?php

namespace App\BoundedContext\Task\Application\Query\GetPaginatedTasks;

use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class GetPaginatedTasksQueryHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function handle(GetPaginatedTasksQuery $query): LengthAwarePaginator
    {
        return $this->taskRepository->findPaginated(
            userId: $query->loggedUser->id,
            page: $query->page,
            perPage: $query->perPage,
            status: $query->status,
            search: $query->search,
            sortBy: $query->sortBy,
            sortOrder: $query->sortOrder,
        );
    }
}
