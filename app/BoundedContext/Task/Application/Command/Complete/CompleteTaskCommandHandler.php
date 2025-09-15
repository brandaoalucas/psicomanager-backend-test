<?php

namespace App\BoundedContext\Task\Application\Command\Complete;

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

class CompleteTaskCommandHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private CreateAuditLogCommandHandler $createAuditLogCommandHandler,
    ) {}

    public function handle(CompleteTaskCommand $command): int
    {
        Logger::started(compact('command'));

        $task = $this->validate($command);

        return DB::transaction(fn() => $this->complete(task: $task, command: $command));
    }

    public function complete(Task $task, CompleteTaskCommand $command): int
    {
        $oldStatus    = $task->status;
        $task->status = TaskStatusEnum::COMPLETED;

        $this->createAuditLog($task, $command, $oldStatus);

        return $task->save();
    }

    private function validate(CompleteTaskCommand $command): Task
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
    private function createAuditLog(Task $task, CompleteTaskCommand $command, TaskStatusEnum $oldStatus): void
    {
        $this->createAuditLogCommandHandler->handle(new CreateAuditLogCommand(
            userId: $command->loggedUser->id,
            referenceId: $command->taskId,
            referenceType: ReferenceType::TASK,
            description: sprintf("Task '%s' foi marcada como concluída (status alterado de %s para %s)",
                $task->title,
                $oldStatus->value,
                TaskStatusEnum::COMPLETED->value
            ),
        ));
    }
}
