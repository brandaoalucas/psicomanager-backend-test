<?php

namespace Tests\Feature\BoundedContext\Task\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\AuditLog\Domain\Entity\AuditLog;
use App\BoundedContext\Task\Domain\Enum\TaskStatusEnum;
use App\Common\Enum\ReferenceType;

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
                "data" => [
                    [
                        "id" => $existentTask->id
                    ]
                ]
            ]);
    }

    protected function sendUpdateTaskRequest(Task $task, array $data): void
    {
        $this
            ->loginById($task->user_id)
            ->putJson("/tasks/{$task->id}", $data)
            ->assertSuccessful();
    }

    protected function sendCompleteTaskRequest(Task $task): void
    {
        $this
            ->loginById($task->user_id)
            ->patchJson("/tasks/{$task->id}/complete")
            ->assertSuccessful();
    }

    protected function sendStartTaskRequest(Task $task): void
    {
        $this
            ->loginById($task->user_id)
            ->patchJson("/tasks/{$task->id}/start")
            ->assertSuccessful();
    }

    protected function sendRestartTaskRequest(Task $task): void
    {
        $this
            ->loginById($task->user_id)
            ->patchJson("/tasks/{$task->id}/restart")
            ->assertSuccessful();
    }

    protected function sendDeleteTaskRequest(Task $task): void
    {
        $this
            ->loginById($task->user_id)
            ->deleteJson("/tasks/{$task->id}")
            ->assertSuccessful();
    }

    protected function sendFindByIdRequest(Task $task, Task $expectedTask): void
    {
        $this
            ->loginById($expectedTask->user_id)
            ->getJson("/tasks/{$task->id}")
            ->assertSuccessful()
            ->assertJson([
                'id' => $expectedTask->id,
                'title' => $expectedTask->title
            ]);
    }

    protected function verifyTaskHasBeenCreatedInDataBase(int $createdTaskId, Task $task): void
    {
        $this->assertDatabaseHas('tasks', [
            'id' => $createdTaskId,
            ...$task->getAttributes(),
        ]);
    }

    protected function verifyTaskHasBeenUpdatedInDataBase(int $taskId, array $updatedData): void
    {
        $this->assertDatabaseHas('tasks', [
            'id' => $taskId,
            ...$updatedData,
        ]);
    }

    protected function verifyTaskStatusInDataBase(int $taskId, TaskStatusEnum $status): void
    {
        $this->assertDatabaseHas('tasks', [
            'id' => $taskId,
            'status' => $status,
        ]);
    }

    protected function verifyTaskHasBeenDeletedFromDataBase(Task $task): void
    {
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
            'deleted_at' => null,
        ]);
    }

    protected function verifyAuditLogWasCreated(int $userId, ReferenceType $referenceType, int $referenceId): void
    {
        $this->assertTrue(
            AuditLog::where('user_id', $userId)
                   ->where('reference_type', $referenceType)
                   ->where('reference_id', $referenceId)
                   ->exists()
        );
    }
}
