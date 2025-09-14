<?php

namespace App\BoundedContext\Iam\Application\Command\User\Create;

use App\BoundedContext\Iam\Application\Command\User\Create\CreateUserCommand;
use App\BoundedContext\Iam\Application\Exception\UserEmailAlreadyExistsException;
use App\BoundedContext\Iam\Application\Exception\UserUsernameAlreadyExistsException;
use App\BoundedContext\Iam\Domain\Repository\UserRepository;
use App\BoundedContext\Iam\Domain\Entity\User;
use App\Helpers\Logger\Logger;
use Illuminate\Support\Facades\DB;

class CreateUserCommandHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function handle(CreateUserCommand $command): User
    {
        Logger::started(compact('comand'));

        $this->validate($command);

        return DB::transaction(fn () => $this->create($command));
    }

    public function validate(CreateUserCommand $command): void
    {
        if (!$this->userRepository->existsByEmail($command->email)) {
            throw new UserEmailAlreadyExistsException(['email' => $command->email]);
        }

        if (!$this->userRepository->existsByUsername($command->username)) {
            throw new UserUsernameAlreadyExistsException(['username' => $command->username]);
        }
    }

    public function create(CreateUserCommand $command): User
    {
        return $this->userRepository->create([
            'name' => $command->name,
            'email' => $command->email,
            'username' => $command->username,
            'password' => $command->password
        ]);
    }
}
