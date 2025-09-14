<?php

namespace App\BoundedContext\AuditLog\Domain\EntityFactory;

use App\BoundedContext\AuditLog\Domain\Entity\AuditLog;
use App\BoundedContext\Iam\Domain\Entity\User;
use App\Common\Enum\ReferenceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    public $model = AuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'reference_type' => $this->faker->randomElement(ReferenceType::cases()),
            'reference_id'   => $this->faker->randomNumber(2),
            'user_id'        => $this->faker->randomNumber(2),
            'description'    => $this->faker->word,
        ];
    }

    public function integration(): self
    {
        return $this->state([
            'user_id' => User::factory()->integration(),
        ]);
    }
}
