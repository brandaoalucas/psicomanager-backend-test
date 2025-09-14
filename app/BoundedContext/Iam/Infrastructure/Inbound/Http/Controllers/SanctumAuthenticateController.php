<?php

namespace App\BoundedContext\Auth\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\Auth\Application\Command\LoginCommandHandler;
use App\BoundedContext\Auth\Infrastructure\Inbound\Http\Requests\CreateSanctumTokenRequest;
use Illuminate\Validation\ValidationException;
use Spatie\RouteAttributes\Attributes\Post;

class SanctumAuthenticateController
{
    public function __construct(
        private readonly LoginCommandHandler $loginCommandHandler,
    ) {}

    /**
     * @throws ValidationException
     */
    #[Post('/sanctum/token')]
    public function createToken(CreateSanctumTokenRequest $request): array
    {
        return $this->loginCommandHandler->handle($request->toCommand());
    }
}
