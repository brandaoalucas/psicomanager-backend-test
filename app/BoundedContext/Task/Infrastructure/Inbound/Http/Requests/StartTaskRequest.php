<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\Task\Application\Command\Start\StartTaskCommand;
use Illuminate\Foundation\Http\FormRequest;

class StartTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toCommand(int $taskId): StartTaskCommand
    {
        return new StartTaskCommand(
            taskId: $taskId,
            loggedUser: $this->user(),
        );
    }
}
