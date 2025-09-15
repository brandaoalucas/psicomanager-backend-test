<?php

namespace Tests\Feature\BoundedContext\Task\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\Task\Infrastructure\Inbound\Http\Controllers\TaskController;
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
    }
}

