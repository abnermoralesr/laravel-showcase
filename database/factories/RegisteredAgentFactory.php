<?php

namespace Database\Factories;

use App\Models\RegisteredAgent;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RegisteredAgent>
 */
class RegisteredAgentFactory extends Factory
{

    protected $model = RegisteredAgent::class;
    private static ?array $stateIsoCodes = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if (self::$stateIsoCodes === null) {
            self::$stateIsoCodes = State::where('iso_code', '<>', 'IL')->pluck('iso_code')->toArray();
        }

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'state' => $this->faker->randomElement(self::$stateIsoCodes),
            'capacity' => $this->faker->numberBetween(5, 15),
        ];
    }
}
