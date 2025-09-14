<?php

namespace Tests\Feature\BoundedContext\Auth\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\Task\Domain\Entity\Task;

trait TestsTaskController
{
    protected function createTask(array $fields = []): Task
    {
        return Task::factory()->create($fields);
    }

    protected function makeTask(array $fields = []): Task
    {
        return Task::factory()->make($fields);
    }

    protected function sendCreateTaskRequest(Task $task): int
    {
        return $this
            ->login()
            ->postJson('/tasks', $task->getAttributes())
            ->assertSuccessful()
            ->json('id');
    }

    protected function verifyTaskHasBeenCreatedInDataBase(int $createdTaskId, Task $task): void
    {
        $this->assertDatabaseHas('tasks', [
            'id' => $createdTaskId,
            ...$task->getAttributes(),
        ]);
    }
}
