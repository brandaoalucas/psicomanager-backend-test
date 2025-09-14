<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\CompleteTaskRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\SaveTaskRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\StartTaskRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\UpdateTaskRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\GetTaskByIdRequest;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests\GetPaginatedTasksRequest;
use App\BoundedContext\Task\Application\Command\Create\CreateTaskCommandHandler;
use App\BoundedContext\Task\Application\Command\Update\UpdateTaskCommandHandler;
use App\BoundedContext\Task\Application\Command\Complete\CompleteTaskCommandHandler;
use App\BoundedContext\Task\Application\Command\Start\StartTaskCommandHandler;
use App\BoundedContext\Task\Application\Command\Restart\RestartTaskCommandHandler;
use App\BoundedContext\Task\Application\Query\GetTaskById\GetTaskByIdQueryHandler;
use App\BoundedContext\Task\Application\Query\GetPaginatedTasks\GetPaginatedTasksQueryHandler;

use ComposableQueryBuilder\Representation\QueryBuilderResult;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Post, Prefix, Put, Patch};

#[Middleware("auth:sanctum")]
#[Prefix("tasks")]
readonly class TaskController
{
    public function __construct(
        private CreateTaskCommandHandler $createTaskCommandHandler,
        private UpdateTaskCommandHandler $updateTaskCommandHandler,
        private CompleteTaskCommandHandler $completeTaskCommandHandler,
        private StartTaskCommandHandler $startTaskCommandHandler,
        private RestartTaskCommandHandler $reopenTaskCommandHandler,
        private GetTaskByIdQueryHandler $getTaskByIdQueryHandler,
        private GetPaginatedTasksQueryHandler $getPaginatedTasksQueryHandler
        // private DeleteTaskCommandHandler $deleteTaskCommandHandler,
    ) {}


    #[Post("/")]
    public function create(SaveTaskRequest $request): Task
    {
        return $this->createTaskCommandHandler->handle($request->toCreateCommand());
    }

    #[Get("/")]
    public function paginate(GetPaginatedTasksRequest $request): QueryBuilderResult
    {
        return $this->getPaginatedTasksQueryHandler->handle($request->toQuery());
    }

    #[Get("/{id}")]
    public function findById(int $taskId, GetTaskByIdRequest $request): Task
    {
        return $this->getTaskByIdQueryHandler->handle($request->toQuery($taskId));
    }

    #[Put("/{id}")]
    public function update(int $taskId, UpdateTaskRequest $request): Task
    {
        return $this->updateTaskCommandHandler->handle($request->toCommand($taskId));
    }

    #[Patch("/{id}/complete")]
    public function complete(int $taskId, CompleteTaskRequest $request): Task
    {
        return $this->completeTaskCommandHandler->handle($request->toCommand($taskId));
    }

    #[Patch("/{id}/start")]
    public function start(int $taskId, StartTaskRequest $request): Task
    {
        return $this->startTaskCommandHandler->handle($request->toCommand($taskId));
    }

    #[Patch("/{id}/reopen")]
    public function reopen(Request $request): Task
    {
        return $this->reopenTaskCommandHandler->handle($request->toCommand());
    }

    // #[Delete("/{id}")]
    // public function destroy(int $id, Request $request): array
    // {
    //     $this->deleteTaskCommandHandler->handle($id, $request->user());

    //     return ['message' => 'Task deleted successfully'];
    // }
}
