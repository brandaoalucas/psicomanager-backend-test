<?php

namespace Tests\Feature\BoundedContext\Task\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\Task\Domain\Enum\TaskStatusEnum;
use App\BoundedContext\Task\Infrastructure\Inbound\Http\Controllers\TaskController;
use App\Common\Enum\ReferenceType;
use Tests\Feature\BoundedContext\Task\Infrastructure\Inbound\Http\Controllers\TestsTaskController;
use Tests\TestCase;
use Tests\Util\Testing;

#[Testing(TaskController::class)]
class TaskControllerIntegrationTest extends TestCase
{
    use TestsTaskController;

    public function test_should_create(): void
    {
        $task          = $this->makeTask();
        $createdTaskId = $this->sendCreateTaskRequest($task);

        $this->verifyTaskHasBeenCreatedInDataBase($createdTaskId, $task);
    }

    public function test_should_paginate(): void
    {
        $existentTask = $this->createTask();

        $this->sendPaginateGetRequest($existentTask);
        $this->sendPaginateGetRequest($existentTask, sprintf('status=%s', $existentTask->status->value));
        $this->sendPaginateGetRequest($existentTask, sprintf('search=%s', urlencode($existentTask->title)));
        $this->sendPaginateGetRequest($existentTask, 'sort_by=title&sort_order=asc');
    }

    public function test_should_update(): void
    {
        $existentTask = $this->createTask();
        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description'
        ];

        $this->sendUpdateTaskRequest($existentTask, $updatedData);
        $this->verifyTaskHasBeenUpdatedInDataBase($existentTask->id, $updatedData);

        $this->verifyAuditLogWasCreated(
            userId: $existentTask->user_id,
            referenceType: ReferenceType::TASK,
            referenceId: $existentTask->id
        );
    }

    public function test_should_complete(): void
    {
        $existentTask = $this->createTask(fields: ['status' => TaskStatusEnum::PENDING]);

        $this->sendCompleteTaskRequest(task: $existentTask);

        $this->verifyTaskStatusInDataBase(
            taskId: $existentTask->id,
            status: TaskStatusEnum::COMPLETED
        );

        $this->verifyAuditLogWasCreated(
            userId: $existentTask->user_id,
            referenceType: ReferenceType::TASK,
            referenceId: $existentTask->id
        );
    }

    public function test_should_start(): void
    {
        $existentTask = $this->createTask(
            fields: [
                'status' => TaskStatusEnum::PENDING
            ]
        );

        $this->sendStartTaskRequest(task: $existentTask);
        $this->verifyTaskStatusInDataBase(
            taskId: $existentTask->id,
            status: TaskStatusEnum::IN_PROGRESS
        );

        $this->verifyAuditLogWasCreated(
            userId: $existentTask->user_id,
            referenceType: ReferenceType::TASK,
            referenceId: $existentTask->id
        );
    }

    public function test_should_restart(): void
    {
        $existentTask = $this->createTask(
            fields: [
                'status' => TaskStatusEnum::COMPLETED
            ]
        );

        $this->sendRestartTaskRequest(task: $existentTask);

        $this->verifyTaskStatusInDataBase(
            taskId: $existentTask->id,
            status: TaskStatusEnum::PENDING
        );

        $this->verifyAuditLogWasCreated(
            userId: $existentTask->user_id,
            referenceType: ReferenceType::TASK,
            referenceId: $existentTask->id
        );
    }

    public function test_should_delete(): void
    {
        $existentTask = $this->createTask();

        $this->sendDeleteTaskRequest(task: $existentTask);

        $this->verifyTaskHasBeenDeletedFromDataBase(task: $existentTask);

        $this->verifyAuditLogWasCreated(
            userId: $existentTask->user_id,
            referenceType: ReferenceType::TASK,
            referenceId: $existentTask->id
        );
    }

    public function test_should_find_by_id(): void
    {
        $existentTask = $this->createTask();

        $this->sendFindByIdRequest(
            task: $existentTask,
            expectedTask: $existentTask
        );
    }
}
