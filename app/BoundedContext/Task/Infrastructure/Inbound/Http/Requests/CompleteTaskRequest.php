<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\Task\Application\Command\Complete\CompleteTaskCommand;
use Illuminate\Foundation\Http\FormRequest;

class CompleteTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toCommand(int $taskId): CompleteTaskCommand
    {
        return new CompleteTaskCommand(
            taskId: $taskId,
            loggedUser: $this->user(),
        );
    }
}
