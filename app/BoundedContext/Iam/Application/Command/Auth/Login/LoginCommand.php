<?php

namespace App\BoundedContext\Iam\Application\Command\Auth\Login;

readonly class LoginCommand
{    public function __construct(
        public string $email,
        public string $password
    ) {}
}
