<?php

namespace App\BoundedContext\Task\Application\Command\Create;

use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommand;
use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommandHandler;
use App\BoundedContext\Task\Application\Command\Create\CreateTaskCommand;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\BoundedContext\Task\Domain\Entity\Task;
use App\Common\Enum\ReferenceType;
use App\Helpers\Logger\Logger;
use Illuminate\Support\Facades\DB;

class CreateTaskCommandHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private CreateAuditLogCommandHandler $createAuditLogCommandHandler,
    ) {}

    public function handle(CreateTaskCommand $command): Task
    {
        Logger::started(compact('command'));

        return DB::transaction(fn() => $this->create($command));
    }

    public function create(CreateTaskCommand $command): Task
    {
        $task = $this->taskRepository->create([
            'user_id'       => $command->loggedUser->id,
            'title'         => $command->title,
            'description'   => $command->description,
            'status'        => $command->status,
        ]);

        $this->createAuditLog($task, $command);

        return $task;
    }

    private function createAuditLog(Task $task, CreateTaskCommand $command): void
    {
        $this->createAuditLogCommandHandler->handle(new CreateAuditLogCommand(
            userId: $command->loggedUser->id,
            referenceId: $task->id,
            referenceType: ReferenceType::TASK,
            description: sprintf("Task '%s' foi criada com status %s",
                $task->title,
                $task->status->value
            ),
        ));
    }
}
