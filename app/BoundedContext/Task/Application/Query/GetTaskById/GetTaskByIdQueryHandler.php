<?php

namespace App\BoundedContext\Task\Application\Query\GetTaskById;

use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Application\Query\GetTaskById\GetTaskByIdQuery;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\BoundedContext\Task\Application\Exception\TaskNotFoundException;
use App\BoundedContext\Task\Application\Exception\TaskAccessDeniedException;

readonly class GetTaskByIdQueryHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function handle(GetTaskByIdQuery $query): Task
    {
        $task = $this->taskRepository->findById($query->taskId);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        if ($task->user_id !== $query->loggedUser->id) {
            throw new TaskAccessDeniedException();
        }

        return $task;
    }
}
