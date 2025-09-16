<?php

namespace App\BoundedContext\Task\Application\Command\Restart;

use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommand;
use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommandHandler;
use App\BoundedContext\Task\Application\Exception\TaskAccessDeniedException;
use App\BoundedContext\Task\Application\Exception\TaskNotFoundException;
use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Domain\Enum\TaskStatusEnum;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\Common\Enum\ReferenceType;
use App\Helpers\Logger\Logger;
use Illuminate\Support\Facades\DB;

class RestartTaskCommandHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private CreateAuditLogCommandHandler $createAuditLogCommandHandler,
    ) {}

    public function handle(RestartTaskCommand $command): int
    {
        Logger::started(compact('command'));

        $task = $this->validate($command);

        return DB::transaction(fn() => $this->restart(task: $task, command: $command));
    }

    public function restart(Task $task, RestartTaskCommand $command): int
    {
        $oldStatus = $task->status;
        $task->status = TaskStatusEnum::PENDING;

        $this->createAuditLog($task, $command, $oldStatus);

        return $task->save();
    }

    private function validate(RestartTaskCommand $command): Task
    {
        $task = $this->taskRepository->findById($command->taskId);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        if ($task->user_id !== $command->loggedUser->id) {
            throw new TaskAccessDeniedException();
        }

        return $task;
    }

    private function createAuditLog(Task $task, RestartTaskCommand $command, TaskStatusEnum $oldStatus): void
    {
        $this->createAuditLogCommandHandler->handle(new CreateAuditLogCommand(
            userId: $command->loggedUser->id,
            referenceId: $command->taskId,
            referenceType: ReferenceType::TASK,
            description: sprintf("Task '%s' foi reaberta (status alterado de %s para %s)",
                $task->title,
                $oldStatus->value,
                TaskStatusEnum::PENDING->value
            ),
        ));
    }
}
