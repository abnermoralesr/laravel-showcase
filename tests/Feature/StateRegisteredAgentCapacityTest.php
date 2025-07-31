<?php

namespace Tests\Feature;

use App\Models\RegisteredAgent;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StateRegisteredAgentCapacityTest extends TestCase
{
    use DatabaseTransactions;

    /* There is not a state 'Dummy State' or iso_code for 'DM', but using this for test to prevent touching existing db states */
    private const TEST_STATE_ISO = 'DM';
    private const TEST_STATE_NAME = 'Dummy State';

    /**
     * A basic feature test example.
     */
    public function test_it_returns_true_when_registered_agents_exist_in_state(): void
    {
        RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 5
        ]);

        $response = $this->getJson('/api/states/' . self::TEST_STATE_ISO . '/registered-agent/capacity');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'state',
                    'name',
                    'email',
                    'capacity',
                ]
            ],
            'state'
        ]);
    }

    public function test_it_returns_empty_when_no_agents_have_capacity(): void
    {
        $agent = RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 1
        ]);
        $agent->companies()->create([
            'user_id' => User::factory()->create()->id,
            'name' => 'Test Co',
            'state' => self::TEST_STATE_ISO
        ]);

        $response = $this->getJson('/api/states/' . self::TEST_STATE_ISO . '/registered-agent/capacity');

        $response->assertOk();
        $response->assertJson([
            'data' => [],
            'state' => self::TEST_STATE_ISO
        ]);
    }
}
