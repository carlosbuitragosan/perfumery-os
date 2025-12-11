<?php

namespace Database\Factories;

use App\Models\Blend;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlendFactory extends Factory
{
    protected $model = Blend::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->unique()->word(),
        ];
    }
}
