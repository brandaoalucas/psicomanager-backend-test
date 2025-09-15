<?php

namespace Tests;

use App\BoundedContext\Iam\Domain\Entity\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected ?User $authenticationUser = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticationUser = null;
    }

    protected function login(array $fields = [])
    {
        $this->authenticationUser ??= User::factory()->create($fields);

        return $this->actingAs($this->authenticationUser);
    }

    protected function loginById(int $userId)
    {
        $this->authenticationUser = User::findOrFail($userId);

        return $this->actingAs($this->authenticationUser);
    }
}
