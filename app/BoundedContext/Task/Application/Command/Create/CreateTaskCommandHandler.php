<?php

namespace App\BoundedContext\Task\Application\Command\Create;

use App\BoundedContext\Task\Application\Command\Create\CreateTaskCommand;
use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\BoundedContext\Task\Domain\Entity\Task;
use App\Helpers\Logger\Logger;
use Illuminate\Support\Facades\DB;

readonly class CreateTaskCommandHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function handle(CreateTaskCommand $command): Task
    {
        Logger::started(compact('command'));

        return DB::transaction(fn() => $this->create($command));
    }

    public function create(CreateTaskCommand $command): Task
    {
        return $this->taskRepository->create([
            'user_id'       => $command->loggedUser->id,
            'title'         => $command->title,
            'description'   => $command->description,
            'status'        => $command->status,
        ]);
    }
}
