<?php

namespace App\BoundedContext\Task\Application\Query\GetPaginatedTasks;


use App\BoundedContext\Task\Application\Query\GetPaginatedTasks\GetPaginatedTasksQuery;
use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;

readonly class GetPaginatedTasksQueryHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function handle(GetPaginatedTasksQuery $query): Task
    {
        return $this->taskRepository->findById($query->loggedUser->id);
    }
}
