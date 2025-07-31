<?php

namespace Tests\Unit;

use App\Enums\RegisteredAgentType;
use App\Events\StateCapacity;
use App\Models\Company;
use App\Models\RegisteredAgent;
use App\Models\User;
use App\Services\RegisteredAgentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class RegisteredAgentServiceTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    private const TEST_COMPANY_NAME = 'Test Co';
    private const TEST_STATE_NO_AGENTS = 'IL';
    /* There is not a state for DM, but using this for test to prevent touching existing db states */
    private const TEST_STATE_ISO = 'DM';
    private const TEST_STATE_NAME = 'Dummy State';

    public function test_returns_agents_with_available_capacity(): void
    {
        $user = User::factory()->create();
        $agent = RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 2,
        ]);

        Company::factory()->create([
            'user_id' => $user->id,
            'registered_agent_id' => $agent->id,
            'registered_agent_type' => RegisteredAgentType::REGISTERED_AGENT->value,
            'state' => self::TEST_STATE_ISO,
        ]);

        $service = new RegisteredAgentService();

        $result = $service->verifyCapacity(self::TEST_STATE_ISO);

        $this->assertCount(1, $result);
        $this->assertEquals($agent->id, $result[0]->id);
    }

    public function test_returns_empty_when_no_agents_have_capacity(): void
    {
        $user = User::factory()->create();
        $agent = RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 1,
        ]);
        Company::factory()->create([
            'user_id' => $user->id,
            'registered_agent_id' => $agent->id,
            'registered_agent_type' => RegisteredAgentType::REGISTERED_AGENT->value,
            'state' => self::TEST_STATE_ISO,
        ]);

        $service = new RegisteredAgentService();

        $result = $service->verifyCapacity(self::TEST_STATE_ISO);

        $this->assertEmpty($result);
    }

    public function test_returns_empty_when_no_agents_exist_in_state(): void
    {
        $service = new RegisteredAgentService();

        $result = $service->verifyCapacity(self::TEST_STATE_NO_AGENTS);

        $this->assertEmpty($result);
    }

    public function test_fires_state_capacity_event(): void
    {
        Event::fake([StateCapacity::class]);

        $user = User::factory()->create();
        $registeredAgent = RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 4
        ]);
        Company::factory()->count(3)->create([
            'user_id' => $user->id,
            'registered_agent_id' => $registeredAgent->id,
            'state' => self::TEST_STATE_ISO
        ]);

        RegisteredAgentService::checkStateAgentCapacity(self::TEST_STATE_ISO);

        Event::assertDispatched(StateCapacity::class);
    }
}
