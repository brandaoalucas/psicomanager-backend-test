<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\Task\Application\Command\Create\CreateTaskCommand;
use App\BoundedContext\Task\Domain\Enum\TaskStatusEnum;
use App\Common\FormRequest\HasTypedFields;
use Illuminate\Foundation\Http\FormRequest;

class SaveTaskRequest extends FormRequest
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
            'status'      => ['sometimes', 'string', 'in:pending,in_progress,completed'],
        ];
    }

    public function toCreateCommand(): CreateTaskCommand
    {
        return new CreateTaskCommand(
            title       : $this->input("title"),
            description : $this->input("description"),
            status      : TaskStatusEnum::from($this->input("status", "pending")),
            loggedUser  : $this->user(),
        );
    }
}
