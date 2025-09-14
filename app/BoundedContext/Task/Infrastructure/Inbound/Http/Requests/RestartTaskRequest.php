<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\Task\Application\Command\Restart\RestartTaskCommand;
use Illuminate\Foundation\Http\FormRequest;

class RestartTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toCommand(int $taskId): RestartTaskCommand
    {
        return new RestartTaskCommand(
            taskId: $taskId,
            loggedUser: $this->user(),
        );
    }
}
