<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $firstDice = $this->faker->numberBetween(1, 6);
        $secondDice = $this->faker->numberBetween(1, 6);
        $victory = ($firstDice + $secondDice == 7);

        return [
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'first_dice' => $firstDice,
            'second_dice' => $secondDice,
            'victory' => $victory,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
