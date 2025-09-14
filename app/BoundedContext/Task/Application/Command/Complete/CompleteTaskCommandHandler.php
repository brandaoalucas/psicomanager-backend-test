<?php

namespace App\BoundedContext\Task\Application\Command\Complete;

use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommand;
use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommandHandler;
use App\BoundedContext\Task\Application\Exception\TaskNotFoundException;
use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\BoundedContext\Task\Domain\Enum\TaskStatus;
use App\Common\Enum\ReferenceType;

class CompleteTaskCommandHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private CreateAuditLogCommandHandler $createAuditLogCommandHandler,
    ) {}

    public function handle(CompleteTaskCommand $command): Task
    {
        $task = $this->taskRepository->findById($command->taskId);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        $oldStatus = $task->status;
        $task->status = TaskStatus::COMPLETED;
        $task->save();

        $this->createAuditLog($task, $command, $oldStatus);

        return $task;
    }

    private function createAuditLog(Task $task, CompleteTaskCommand $command, TaskStatus $oldStatus): void
    {
        $this->createAuditLogCommandHandler->handle(new CreateAuditLogCommand(
            userId: $command->loggedUser->id,
            referenceId: $command->taskId,
            referenceType: ReferenceType::TASK,
            description: sprintf("Task '%s' foi marcada como concluída (status alterado de %s para %s)",
                $task->title,
                $oldStatus->value,
                TaskStatus::COMPLETED->value
            ),
        ));
    }
}
