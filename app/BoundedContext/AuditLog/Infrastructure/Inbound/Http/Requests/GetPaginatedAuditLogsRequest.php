<?php

namespace App\BoundedContext\AuditLog\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\AuditLog\Application\Query\GetPaginatedAuditLogs\GetPaginatedAuditLogsQuery;
use App\Common\Enum\ReferenceType;
use Illuminate\Foundation\Http\FormRequest;

class GetPaginatedAuditLogsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'sometimes|string',
            'user_id' => 'sometimes|integer',
            'action' => 'sometimes|string',
            'reference_type' => 'sometimes|string',
            'limit' => 'sometimes|integer|min:1|max:100',
        ];
    }

    public function toQuery(): GetPaginatedAuditLogsQuery
    {
        return new GetPaginatedAuditLogsQuery(
            id           : $this->input("id"),
            userName     : $this->input("user_name"),
            referenceType: ReferenceType::fromSafely($this->input("reference_type")),
            referenceId  : $this->input("reference_id"),
        );
    }
}
