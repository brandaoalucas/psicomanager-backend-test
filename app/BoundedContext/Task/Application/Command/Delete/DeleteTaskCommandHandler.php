<?php

namespace App\BoundedContext\Task\Application\Command\Delete;

use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommand;
use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommandHandler;
use App\BoundedContext\Task\Application\Command\Delete\DeleteTaskCommand;
use App\BoundedContext\Task\Application\Exception\TaskNotFoundException;
use App\BoundedContext\Task\Application\Exception\TaskAccessDeniedException;
use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\Common\Enum\ReferenceType;
use App\Helpers\Logger\Logger;
use Illuminate\Support\Facades\DB;

class DeleteTaskCommandHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private CreateAuditLogCommandHandler $createAuditLogCommandHandler,
    ) {}

    public function handle(DeleteTaskCommand $command): int
    {
        Logger::started(compact('command'));

        $task = $this->validate($command);

        return DB::transaction(fn() => $this->delete(task: $task, command: $command));
    }

    public function delete(Task $task, DeleteTaskCommand $command): int
    {
        $this->createAuditLog($task, $command);

        return $task->delete();
    }

    private function validate(DeleteTaskCommand $command): Task
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

    private function createAuditLog(Task $task, DeleteTaskCommand $command): void
    {
        $this->createAuditLogCommandHandler->handle(new CreateAuditLogCommand(
            userId: $command->loggedUser->id,
            referenceId: $command->taskId,
            referenceType: ReferenceType::TASK,
            description: sprintf("Task '%s' foi excluída", $task->title),
        ));
    }
}
