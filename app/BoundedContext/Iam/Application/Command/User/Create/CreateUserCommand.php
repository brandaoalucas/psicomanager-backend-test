<?php

namespace App\BoundedContext\Iam\Application\Command\User\Create;

use App\Common\Contract\Command;

class CreateUserCommand extends Command
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $username,
    ) {}
}
