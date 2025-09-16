<?php

namespace App\BoundedContext\Task\Application\Command\Update;

use App\BoundedContext\Task\Application\Exception\TaskAccessDeniedException;
use App\BoundedContext\Task\Application\Exception\TaskNotFoundException;
use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommand;
use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommandHandler;
use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\Common\Enum\ReferenceType;
use App\Helpers\Logger\Logger;
use Illuminate\Support\Facades\DB;

readonly class UpdateTaskCommandHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private CreateAuditLogCommandHandler $createAuditLogCommandHandler,
    ) {}

    public function handle(UpdateTaskCommand $command): int
    {
        Logger::started(compact('command'));

        $task = $this->validate($command);

        return DB::transaction(fn() => $this->update(task: $task, command: $command));
    }

    public function update(Task $task, UpdateTaskCommand $command): int
    {
        $oldTitle = $task->title;
        $oldDescription = $task->description;

        $task->fill([
            'title' => $command->title,
            'description' => $command->description,
            'updated_by' => $command->loggedUser->id,
        ]);

        $this->createAuditLog($task, $command, $oldTitle, $oldDescription);

        return $task->save();
    }

    private function validate(UpdateTaskCommand $command): Task
    {
        $task = $this->taskRepository->findById($command->id);
        if (!$task) {
            throw new TaskNotFoundException();
        }

        if ($task->user_id !== $command->loggedUser->id) {
            throw new TaskAccessDeniedException();
        }

        return $task;
    }

    private function createAuditLog(Task $task, UpdateTaskCommand $command, string $oldTitle, string $oldDescription): void
    {
        $this->createAuditLogCommandHandler->handle(new CreateAuditLogCommand(
            userId: $command->loggedUser->id,
            referenceId: $command->id,
            referenceType: ReferenceType::TASK,
            description: sprintf("Task '%s' foi atualizada (título alterado de '%s' para '%s', descrição alterada de '%s' para '%s')",
                $task->title,
                $oldTitle,
                $command->title,
                $oldDescription,
                $command->description,
            ),
        ));
    }
}
