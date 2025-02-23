<?php

namespace Database\Factories;

use App\Enums\EProfileStatus;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'image' => 'images/default.png', // Remplace par une logique de fichier si nécessaire
            'status' => fake()->randomElement(EProfileStatus::cases()),
        ];
    }
}
