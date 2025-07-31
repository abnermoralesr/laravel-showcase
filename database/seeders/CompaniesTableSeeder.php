<?php

namespace Database\Seeders;

use App\Enums\RegisteredAgentType;
use App\Models\Company;
use App\Models\RegisteredAgent;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Company::get()->isEmpty()) {
            return;
        }

        $users = User::all();
        $states = State::all()->keyBy('iso_code');
        $agentsByState = RegisteredAgent::all()->groupBy('state');
        $agentCapacities = [];
        $companiesToInsert = [];

        foreach ($users as $user) {
            $numCompanies = rand(1, 3);

            for ($i = 0; $i < $numCompanies; $i++) {
                $state = $states->random();
                $agentType = RegisteredAgentType::REGISTERED_AGENT;
                $agentId = null;

                if ($state->iso_code === 'IL') {
                    $agentType = RegisteredAgentType::USER;
                    $agentId = $user->id;
                } else {
                    $agents = $agentsByState[$state->iso_code] ?? collect();
                    $eligibleAgents = $agents->filter(function ($agent) use ($agentCapacities) {
                        return ($agentCapacities[$agent->id] ?? 0) < $agent->capacity;
                    });

                    if ($eligibleAgents->isNotEmpty()) {
                        $chosenAgent = $eligibleAgents->random();
                        $agentId = $chosenAgent->id;
                        $agentCapacities[$agentId] = ($agentCapacities[$agentId] ?? 0) + 1;
                    } else {
                        $agentType = RegisteredAgentType::USER;
                        $agentId = $user->id;
                    }
                }

                $companiesToInsert[] = Company::factory()->make([
                    'user_id' => $user->id,
                    'state' => $state->iso_code,
                    'registered_agent_type' => $agentType,
                    'registered_agent_id' => $agentId,
                ])->toArray();;
            }
        }

        Company::insert($companiesToInsert);
        $timestamp = Carbon::parse('now')->format('Y-m-d H:i:s');
        Company::whereNull('created_at')->update([
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);
    }
}
