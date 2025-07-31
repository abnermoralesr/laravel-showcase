<?php

namespace Database\Seeders;

use App\Models\RegisteredAgent;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegisteredAgentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!RegisteredAgent::get()->isEmpty()) {
            return;
        }

        $statesWithTwoAgents = ['California', 'Texas'];
        $states = State::get();
        $agentsToInsert = [];

        foreach ($states as $state) {
            if ($state->iso_code == 'IL') {
                continue;
            }

            if (in_array($state->name, $statesWithTwoAgents)) {
                for ($i = 0; $i < 2; $i++) {
                    $agentsToInsert[] = $this->makeDummyAgent($state->iso_code);
                }

                continue;
            }

            $agentsToInsert[] = $this->makeDummyAgent($state->iso_code);
        }

        RegisteredAgent::insert($agentsToInsert);
        $timestamp = Carbon::parse('now')->format('Y-m-d H:i:s');
        RegisteredAgent::whereNull('created_at')->update([
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);
    }

    /**
     * @param string $stateIsoCode
     * @return array
     */
    private function makeDummyAgent(string $stateIsoCode): array
    {
        return RegisteredAgent::factory()->make([
            'state' => $stateIsoCode,
        ])->toArray();
    }
}
