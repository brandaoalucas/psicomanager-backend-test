<?php

namespace App\BoundedContext\Task\Domain\EntityFactory;

use App\BoundedContext\Iam\Domain\Entity\User;
use App\BoundedContext\Task\Domain\Entity\Task;
use App\BoundedContext\Task\Domain\Enum\TaskStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'         => $this->faker->title(),
            'description'   => $this->faker->text(),
            'status'        => $this->faker->randomElement(TaskStatusEnum::cases()),
        ];
    }

    /**
     * Estado para integração (para testes de integração)
     */
    public function integration(): static
    {
        return $this->state([
            'user_id'   => User::factory(),
        ]);
    }

}
