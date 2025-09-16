<?php

namespace App\BoundedContext\Iam\Application\Command\Auth\Login;

use App\BoundedContext\Iam\Application\Service\AuthCredentialChecker;
use App\BoundedContext\Iam\Application\Service\LoginAttemptService;
use App\BoundedContext\Iam\Application\Command\Auth\Login\LoginCommand;
use Illuminate\Validation\ValidationException;

class LoginCommandHandler
{
    public function __construct(
        private readonly AuthCredentialChecker $authCredentialChecker,
    ) {}

    public function handle(LoginCommand $command): array
    {
        $token = $this->verifyUser($command);

        return ['bearer_token' => $token];
    }
    private function verifyUser(LoginCommand $command): string
    {
        $user = $this->authCredentialChecker->verify($command->email, $command->password);

        if (! $user) {
            $message = ['email' => ['O email e/ou senha não são válidos!']];

            throw ValidationException::withMessages($message);
        }

        $token = $user->createToken('api-token');

        return $token->plainTextToken;
    }
}
