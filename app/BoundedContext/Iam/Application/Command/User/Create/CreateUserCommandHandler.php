<?php

namespace App\BoundedContext\Iam\Application\Command\User\Create;

use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommand;
use App\BoundedContext\AuditLog\Application\Command\Create\CreateAuditLogCommandHandler;
use App\BoundedContext\Iam\Application\Command\User\Create\CreateUserCommand;
use App\BoundedContext\Iam\Application\Exception\UserEmailAlreadyExistsException;
use App\BoundedContext\Iam\Application\Exception\UserUsernameAlreadyExistsException;
use App\BoundedContext\Iam\Domain\Repository\UserRepository;
use App\BoundedContext\Iam\Domain\Entity\User;
use App\Common\Enum\ReferenceType;
use App\Helpers\Logger\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private CreateAuditLogCommandHandler $createAuditLogCommandHandler,
    ) {}

    public function handle(CreateUserCommand $command): User
    {
        Logger::started(compact('command'));

        $this->validate($command);

        return DB::transaction(fn () => $this->create($command));
    }

    public function create(CreateUserCommand $command): User
    {
        $user = $this->userRepository->create([
            'name' => $command->name,
            'email' => $command->email,
            'username' => $command->username,
            'password' => Hash::make($command->password)
        ]);

        $this->createAuditLog($user, $command);

        return $user;
    }

    private function validate(CreateUserCommand $command): void
    {
        if ($this->userRepository->existsByEmail($command->email)) {
            throw new UserEmailAlreadyExistsException(['email' => $command->email]);
        }

        if ($this->userRepository->existsByUsername($command->username)) {
            throw new UserUsernameAlreadyExistsException(['username' => $command->username]);
        }
    }

    private function createAuditLog(User $user, CreateUserCommand $command): void
    {
        $this->createAuditLogCommandHandler->handle(new CreateAuditLogCommand(
            userId: $user->id,
            referenceId: $user->id,
            referenceType: ReferenceType::USER,
            description: sprintf("Usuário '%s' foi criado com email %s",
                $user->name,
                $user->email
            ),
        ));
    }
}
