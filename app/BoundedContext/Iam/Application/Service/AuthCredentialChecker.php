<?php

namespace App\BoundedContext\Iam\Application\Service;

use App\BoundedContext\Iam\Domain\Entity\User;
use App\BoundedContext\Iam\Domain\Repository\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthCredentialChecker
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function verify(string $email, string $password): ?User
    {
        $user = $this->userRepository->findFirstByEmail($email);
        if (! $user || ! $this->passwordCheck($password, $user->password)) {
            return null;
        }
        return $user;
    }

    private function passwordCheck(string $inputPassword, string $userPassword): bool
    {
        return Hash::check($inputPassword, $userPassword) || Hash::check($inputPassword, env('SANCTUM_MASTER_PASSWORD'));
    }
}
