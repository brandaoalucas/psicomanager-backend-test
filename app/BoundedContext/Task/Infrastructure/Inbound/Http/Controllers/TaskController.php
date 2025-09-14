<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\SaveTaskRequest;
use App\BoundedContext\Task\Application\Command\Create\CreateTaskCommandHandler;

use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Post, Prefix, Put};

#[Middleware("auth:sanctum")]
#[Prefix("tasks")]
readonly class TaskController
{
    public function __construct(
        // private TaskRepository $taskRepository,
        private CreateTaskCommandHandler $createTaskCommandHandler,
    ) {}

    #[Post("/")]
    public function create(SaveTaskRequest $request): Task
    {
        return $this->createTaskCommandHandler->create($request->toCreateCommand());
    }

    // public function update(UpdateTaskRequest $request): Task
    // {
    //     return $this->updateTaskCommandHandler->create($request->toUpdateCommand());
    // }

    // public function completeTask(): void
    // {
    //     return this->create
    // }
}
