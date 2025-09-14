<?php

namespace App\BoundedContext\Iam\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\Iam\Application\Command\User\Create\CreateUserCommand;
use App\BoundedContext\Iam\Domain\Enum\UserType;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
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
            'name'                                           => 'string|required|min:3|max:255',
            'email'                                          => 'string|required',
            'username'                                       => 'string|nullable',
            'password'                                       => 'required',
        ];
    }

    public function toCreateCommand(): CreateUserCommand
    {
        return new CreateUserCommand(
            name                                      : $this->input('name'),
            email                                     : $this->input('email'),
            username                                  : $this->input('username'),
            password                                  : $this->input('password'),
        );
    }
}
