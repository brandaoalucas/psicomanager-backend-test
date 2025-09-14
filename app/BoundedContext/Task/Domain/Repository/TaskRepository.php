<?php

namespace App\BoundedContext\Task\Domain\Repository;

use App\BoundedContext\Task\Domain\Entity\Task;

class TaskRepository
{
    public function findById(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function create(array $fields): Task
    {
        return Task::create($fields);
    }
}
