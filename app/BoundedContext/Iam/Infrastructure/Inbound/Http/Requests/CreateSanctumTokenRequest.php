<?php

namespace App\BoundedContext\Auth\Infrastructure\Inbound\Http\Requests;

use App\BoundedContext\Iam\Application\Command\Auth\Login\LoginCommand;
use Illuminate\Foundation\Http\FormRequest;

class CreateSanctumTokenRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'    => 'required|email',
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'email'    => 'O e-mail informado não é válido.',
            'password' => 'O campo senha é obrigatório.',
        ];
    }

    public function toCommand(): LoginCommand
    {
        return new LoginCommand(
            email        : $this->input('email'),
            password     : $this->input('password'),
        );
    }
}
