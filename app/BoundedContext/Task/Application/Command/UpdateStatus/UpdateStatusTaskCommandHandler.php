<?php

namespace App\BoundedContext\Task\Application\Command\UpdateStatus;

use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommand;
use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommandHandler;
use App\BoundedContext\Task\Application\Exception\TaskNotFoundException;
use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\Common\Enum\ReferenceType;
use App\Helpers\Logger\Logger;

class UpdateStatusTaskCommandHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private CreateAuditLogCommandHandler $createAuditLogCommandHandler,
    ) {}

    public function handle(UpdateStatusTaskCommand $command): Task
    {
        Logger::started(compact('command'));

        $task = $this->taskRepository->findById($command->id);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        $this->createAuditLog(
            task    : $task,
            command : $command
        );

        $task->status = $command->status;

        return $task->save();
    }

    private function createAuditLog(Task $task, UpdateStatusTaskCommand $command): void
    {
        $description = sprintf("O status da task foi alterado de %s para %s", $task->status, $command->status);

        $this->createAuditLogCommandHandler->handle(new CreateAuditLogCommand(
            userId        : $command->loggedUser->id,
            referenceId   : $command->id,
            referenceType : ReferenceType::TASK,
            description   : $description
        ));
    }
}
