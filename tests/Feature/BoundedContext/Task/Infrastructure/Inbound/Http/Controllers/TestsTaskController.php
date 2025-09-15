<?php

namespace Tests\Feature\BoundedContext\Task\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\Task\Domain\Entity\Task;

trait TestsTaskController
{
    protected function createTask(array $fields = []): Task
    {
        return Task::factory()->integration()->create($fields);
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

    protected function sendPaginateGetRequest(Task $existentTask, ?string $filter = null): void
    {
        $route = filled($filter)
            ? sprintf('/tasks?%s', $filter)
            : '/tasks';

        $this
            ->loginById($existentTask->user_id)
            ->getJson($route)
            ->assertSuccessful()
            ->assertJson([
                "current_page" => 1,
                "data" => [
                    "id" => $existentTask->id
                ]
            ]);
    }

    protected function verifyTaskHasBeenCreatedInDataBase(int $createdTaskId, Task $task): void
    {
        $this->assertDatabaseHas('tasks', [
            'id' => $createdTaskId,
            ...$task->getAttributes(),
        ]);
    }
}
