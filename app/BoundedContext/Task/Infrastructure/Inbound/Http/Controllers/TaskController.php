<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\CompleteTaskRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\SaveTaskRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\StartTaskRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\UpdateTaskRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\GetTaskByIdRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\GetPaginatedTasksRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\RestartTaskRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\DeleteTaskRequest;
use App\BoundedContext\Task\Application\Command\Create\CreateTaskCommandHandler;
use App\BoundedContext\Task\Application\Command\Update\UpdateTaskCommandHandler;
use App\BoundedContext\Task\Application\Command\Complete\CompleteTaskCommandHandler;
use App\BoundedContext\Task\Application\Command\Start\StartTaskCommandHandler;
use App\BoundedContext\Task\Application\Command\Restart\RestartTaskCommandHandler;
use App\BoundedContext\Task\Application\Command\Delete\DeleteTaskCommandHandler;
use App\BoundedContext\Task\Application\Query\GetTaskById\GetTaskByIdQueryHandler;
use App\BoundedContext\Task\Application\Query\GetPaginatedTasks\GetPaginatedTasksQueryHandler;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Post, Prefix, Put, Patch};

#[Middleware("auth:sanctum")]
#[Prefix("/tasks")]
readonly class TaskController
{
    public function __construct(
        private CreateTaskCommandHandler $createTaskCommandHandler,
        private UpdateTaskCommandHandler $updateTaskCommandHandler,
        private CompleteTaskCommandHandler $completeTaskCommandHandler,
        private StartTaskCommandHandler $startTaskCommandHandler,
        private RestartTaskCommandHandler $restartTaskCommandHandler,
        private GetTaskByIdQueryHandler $getTaskByIdQueryHandler,
        private GetPaginatedTasksQueryHandler $getPaginatedTasksQueryHandler,
        private DeleteTaskCommandHandler $deleteTaskCommandHandler,
    ) {}


    #[Post("/")]
    public function create(SaveTaskRequest $request): Task
    {
        return $this->createTaskCommandHandler->handle(command: $request->toCreateCommand());
    }

    #[Get("/")]
    public function paginate(GetPaginatedTasksRequest $request): LengthAwarePaginator
    {
        return $this->getPaginatedTasksQueryHandler->handle(query: $request->toQuery());
    }

    #[Get("/{taskId}")]
    public function findById(int $taskId, GetTaskByIdRequest $request): Task
    {
        return $this->getTaskByIdQueryHandler->handle(query: $request->toQuery(taskId: $taskId));
    }

    #[Put("/{taskId}")]
    public function update(int $taskId, UpdateTaskRequest $request): int
    {
        return $this->updateTaskCommandHandler->handle(command: $request->toCommand(taskId: $taskId));
    }

    #[Patch("/{taskId}/complete")]
    public function complete(int $taskId, CompleteTaskRequest $request): int
    {
        return $this->completeTaskCommandHandler->handle(command: $request->toCommand(taskId: $taskId));
    }

    #[Patch("/{taskId}/start")]
    public function start(int $taskId, StartTaskRequest $request): int
    {
        return $this->startTaskCommandHandler->handle(command: $request->toCommand(taskId: $taskId));
    }

    #[Patch("/{taskId}/restart")]
    public function restart(int $taskId, RestartTaskRequest $request): int
    {
        return $this->restartTaskCommandHandler->handle(command: $request->toCommand(taskId: $taskId));
    }

    #[Delete("/{taskId}")]
    public function destroy(int $taskId, DeleteTaskRequest $request): int
    {
        return $this->deleteTaskCommandHandler->handle(command: $request->toCommand(taskId: $taskId));
    }
}
