<?php

namespace App\BoundedContext\Task\Application\Command\Delete;

use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommand;
use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommandHandler;
use App\BoundedContext\Task\Application\Command\Delete\DeleteTaskCommand;
use App\BoundedContext\Task\Application\Exception\TaskNotFoundException;
use App\BoundedContext\Task\Application\Exception\TaskAccessDeniedException;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\BoundedContext\Iam\Domain\Entity\User;
use App\Common\Enum\ReferenceType;

class DeleteTaskCommandHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private CreateAuditLogCommandHandler $createAuditLogCommandHandler,
    ) {}

    public function handle(DeleteTaskCommand $command): int
    {
        $task = $this->taskRepository->findById($command->taskId);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        if ($task->user_id !== $command->loggedUser->id) {
            throw new TaskAccessDeniedException();
        }

        $this->createAuditLog($task, $command->loggedUser);

        $this->taskRepository->delete($taskId);
    }

    private function createAuditLog($task, User $loggedUser): void
    {
        $this->createAuditLogCommandHandler->handle(new CreateAuditLogCommand(
            userId: $loggedUser->id,
            referenceId: $task->id,
            referenceType: ReferenceType::TASK,
            description: sprintf("Task '%s' foi excluída", $task->title),
        ));
    }
}
