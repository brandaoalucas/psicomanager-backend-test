<?php

namespace App\BoundedContext\Iam\Domain\Repository;

use App\BoundedContext\Iam\Domain\Entity\User;

class UserRepository
{
    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }
    public function existsByEmail(string $email): bool
    {
        return User::query()
            ->where("email", $email)
            ->exists();
    }

    public function findFirstByEmail(string $email): ?User
    {
        return User::query()
            ->where("email", $email)
            ->first();
    }

    public function existsByUsername(string $username): bool
    {
        return User::query()
            ->where("username", $username)
            ->exists();
    }

    public function create(array $attrs): User
    {
        return User::create($attrs);
    }
}
