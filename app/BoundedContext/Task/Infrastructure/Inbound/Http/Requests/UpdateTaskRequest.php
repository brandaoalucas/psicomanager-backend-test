<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\Task\Application\Command\Update\UpdateTaskCommand;
use App\Common\FormRequest\HasTypedFields;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    use HasTypedFields;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
        ];
    }

    public function toCommand(int $taskId): UpdateTaskCommand
    {
        return new UpdateTaskCommand(
            id          : $taskId,
            title       : $this->input("title"),
            description : $this->input("description"),
            loggedUser  : $this->user(),
        );
    }
}
