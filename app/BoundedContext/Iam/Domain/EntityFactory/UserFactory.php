<?php

namespace App\BoundedContext\Iam\Domain\EntityFactory;

use App\BoundedContext\Iam\Domain\Entity\BusinessPartner;
use App\BoundedContext\Iam\Domain\Entity\User;
use App\BoundedContext\Iam\Domain\Entity\UserProfile;
use App\BoundedContext\Iam\Domain\Enum\UserType;
use App\BoundedContext\Organization\Domain\Entity\SalesTeam;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'                                           => fake()->name(),
            'email'                                          => fake()->safeEmail(),
            'username'                                       => $this->faker->name,
            'password'                                       => Hash::make('12345'),
            'remember_token'                                 => Str::random(10),
        ];
    }
}
