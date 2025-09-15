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
        return [
            'page'       => ['sometimes', 'integer', 'min:1'],
            'per_page'   => ['sometimes', 'integer', 'min:1', 'max:100'],
            'status'     => ['sometimes', 'string', 'in:pending,in_progress,completed'],
            'search'     => ['sometimes', 'string', 'max:255'],
            'sort_by'    => ['sometimes', 'string', 'in:created_at,updated_at,title,status,user_name,days_since_created'],
            'sort_order' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }

    public function toQuery(): GetPaginatedTasksQuery
    {
        return new GetPaginatedTasksQuery(
            loggedUser: $this->user(),
            page: (int) $this->input('page', 1),
            perPage: (int) $this->input('per_page', 15),
            status: $this->input('status'),
            search: $this->input('search'),
            sortBy: $this->input('sort_by', 'created_at'),
            sortOrder: $this->input('sort_order', 'desc'),
        );
    }
}
