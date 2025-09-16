<?php

namespace App\BoundedContext\Iam\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\Iam\Application\Command\Auth\Login\LoginCommandHandler;
use App\BoundedContext\Iam\Infrastructure\Inbound\Http\Requests\CreateSanctumTokenRequest;
use Spatie\RouteAttributes\Attributes\Post;

class SanctumAuthenticateController
{
    public function __construct(
        private readonly LoginCommandHandler $loginCommandHandler,
    ) {}

    #[Post('/sanctum/token')]
    public function createToken(CreateSanctumTokenRequest $request): array
    {
        return $this->loginCommandHandler->handle($request->toCommand());
    }
}
