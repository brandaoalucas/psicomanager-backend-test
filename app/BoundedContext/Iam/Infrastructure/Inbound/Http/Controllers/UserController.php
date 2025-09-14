<?php

namespace App\BoundedContext\Iam\Infrastructure\Inbound\Http\Controllers;

use App\BoundedContext\Iam\Application\Command\User\Create\CreateUserCommandHandler;
use App\BoundedContext\Iam\Domain\Entity\User;
use App\BoundedContext\Iam\Infrastructure\Inbound\Http\Requests\CreateUserRequest;
use Spatie\RouteAttributes\Attributes\{Post, Prefix};

#[Prefix("/users")]
readonly class UserController
{
    public function __construct(
        private CreateUserCommandHandler $createUserCommandHandler,
    ) {}

    #[Post("/")]
    public function create(CreateUserRequest $request): User
    {
        return $this->createUserCommandHandler->handle($request->toCreateCommand());
    }
}
