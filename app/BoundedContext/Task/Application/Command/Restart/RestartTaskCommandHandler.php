<?php

namespace App\BoundedContext\Task\Application\Command\Restart;

use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommand;
use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommandHandler;
use App\BoundedContext\Task\Application\Exception\TaskNotFoundException;
use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\BoundedContext\Task\Domain\Enum\TaskStatus;
use App\Common\Enum\ReferenceType;

class RestartTaskCommandHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private CreateAuditLogCommandHandler $createAuditLogCommandHandler,
    ) {}

    public function handle(RestartTaskCommand $command): Task
    {
        $task = $this->taskRepository->findById($command->taskId);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        $oldStatus = $task->status;
        $task->status = TaskStatus::PENDING;
        $task->save();

        $this->createAuditLog($task, $command, $oldStatus);

        return $task;
    }

    private function createAuditLog(Task $task, RestartTaskCommand $command, TaskStatus $oldStatus): void
    {
        $this->createAuditLogCommandHandler->handle(new CreateAuditLogCommand(
            userId: $command->loggedUser->id,
            referenceId: $command->taskId,
            referenceType: ReferenceType::TASK,
            description: sprintf("Task '%s' foi reaberta (status alterado de %s para %s)",
                $task->title,
                $oldStatus->value,
                TaskStatus::PENDING->value
            ),
        ));
    }
}
