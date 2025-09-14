<?php

namespace App\BoundedContext\Task\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\Task\Application\Command\Create\CreateTaskCommand;
use App\Common\FormRequest\HasTypedFields;
use Illuminate\Foundation\Http\FormRequest;
use TaskStatus;

class SaveTaskRequest extends FormRequest
{
    use HasTypedFields;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'     => ['required', 'integer', 'exists:users,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'status'      => ['required', 'enum:TaskStatus'],
        ];
    }

    public function toCreateCommand(): CreateTaskCommand
    {
        return new CreateTaskCommand(
            loggedUser     : $this->user(),
            title          : $this->input("title"),
            description    : $this->input("description"),
            status         : $this->enum("status", TaskStatus::class),
        );
    }
}
