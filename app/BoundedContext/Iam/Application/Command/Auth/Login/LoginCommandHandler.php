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
        $ip = request()->ip();

        // $this->verifyIsBlockedAttempt($ip);

        $token = $this->verifyUser($command, $ip);

        LoginAttemptService::clear($ip);

        return ['bearer_token' => $token];
    }

    private function verifyIsBlockedAttempt(?string $ip): void
    {
        if (LoginAttemptService::isBlockedAttempt($ip)) {
            $message = ['email' => ['Limite de tentativas atingido. Tente novamente mais tarde']];

            throw ValidationException::withMessages($message);
        }
    }
    private function verifyUser(LoginCommand $command, string $ip): string
    {
        $user = $this->authCredentialChecker->verify($command->email, $command->password);

        if (! $user) {

            LoginAttemptService::increment($ip);

            $message = ['email' => ['O email e/ou senha não são válidos!']];

            throw ValidationException::withMessages($message);
        }

        $token = $user->createToken('api-token');

        return $token->plainTextToken;
    }
}
