<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\Task\Application\Query\GetPaginatedTasks\GetPaginatedTasksQuery;
use Illuminate\Foundation\Http\FormRequest;

class GetPaginatedTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toQuery(int $taskId): GetPaginatedTasksQuery
    {
        return new GetPaginatedTasksQuery(
            taskId: $taskId,
            loggedUser: $this->user(),
        );
    }
}
