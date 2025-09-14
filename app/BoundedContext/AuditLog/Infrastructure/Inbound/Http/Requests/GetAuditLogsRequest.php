<?php

namespace App\BoundedContext\AuditLog\Infrastructure\Inbound\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetAuditLogsRequest extends FormRequest
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
}
