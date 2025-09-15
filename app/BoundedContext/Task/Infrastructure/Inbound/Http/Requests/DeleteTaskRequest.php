<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\Task\Application\Command\Delete\DeleteTaskCommand;

use Illuminate\Foundation\Http\FormRequest;

class DeleteTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function toCommand(int $taskId): DeleteTaskCommand
    {
        return new DeleteTaskCommand(
            taskId: $taskId,
            loggedUser: $this->user(),
        );
    }
}
