<?php

namespace Database\Factories;

use App\Enums\RegisteredAgentType;
use App\Models\Company;
use App\Models\RegisteredAgent;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;
    private static ?array $stateIsoCodes = null;
    private static ?array $userIds = null;
    private static ?array $registeredAgentIds = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->initRelatedTables();

        $state = State::inRandomOrder()->first();
        $agent = RegisteredAgent::where('state', $state->iso_code)->inRandomOrder()->first();
        $userId = $this->faker->randomElement(self::$userIds);
        $registeredAgentId = $userId;
        $registeredAgentType = RegisteredAgentType::USER;

        if (!empty($agent->id)) {
            $registeredAgentId = $agent->id;
            $registeredAgentType = RegisteredAgentType::REGISTERED_AGENT;
        }

        return [
            'user_id' => $userId,
            'name' => $this->faker->company(),
            'state' => $state->iso_code,
            'registered_agent_id' => $registeredAgentId,
            'registered_agent_type' => $registeredAgentType
        ];
    }

    private function initRelatedTables(): void {
        if (self::$stateIsoCodes === null) {
            self::$stateIsoCodes = State::pluck('iso_code')->toArray();
        }

        if (self::$userIds === null) {
            self::$userIds = User::pluck('id')->toArray();
        }

        if (self::$registeredAgentIds === null) {
            self::$registeredAgentIds = RegisteredAgent::pluck('id')->toArray();
        }
    }
}
