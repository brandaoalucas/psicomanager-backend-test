<?php

namespace App\BoundedContext\Task\Application\Query\GetTaskById;

use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Application\Query\GetTaskById\GetTaskByIdQuery;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;

readonly class GetTaskByIdQueryHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function handle(GetTaskByIdQuery $query): Task
    {
        return $this->taskRepository->findById($query->loggedUser->id);
    }
}
