<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\Task\Application\Query\GetTaskById\GetTaskByIdQuery;

use Illuminate\Foundation\Http\FormRequest;

class GetTaskByIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toQuery(int $taskId): GetTaskByIdQuery
    {
        return new GetTaskByIdQuery(
            taskId: $taskId,
            loggedUser: $this->user(),
        );
    }
}
