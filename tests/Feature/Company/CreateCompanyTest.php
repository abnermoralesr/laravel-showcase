<?php

namespace Tests\Feature\Company;

use App\Enums\RegisteredAgentType;
use App\Models\RegisteredAgent;
use App\Models\State;
use App\Models\User;
use Database\Factories\CompanyFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateCompanyTest extends TestCase
{
    use DatabaseTransactions;

    private const TEST_COMPANY_NAME = 'Test Co';
    /* There is not a state 'Dummy State' or iso_code for 'DM', but using this for test to prevent touching existing db states */
    private const TEST_STATE_ISO = 'DM';
    private const TEST_STATE_NAME = 'Dummy State';
    private const TEST_STATE_ISO_FALLBACK = 'IL';

    protected function setUp(): void
    {
        parent::setUp();

        CompanyFactory::setStateIsoCodes(State::pluck('iso_code')->toArray());
        CompanyFactory::setUserIds(User::get()->toArray());
        CompanyFactory::setRegisteredAgentIds(RegisteredAgent::pluck('id')->toArray());
    }

    public function test_user_can_create_company(): void
    {
        $this->createDummyState();
        $user = User::factory()->create();
        $registeredAgent = RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 3
        ]);
        $this->actingAs($user);
        $payload = [
            'user_id' => $user->id,
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO,
            'self_assigned' => null
        ];

        $response = $this->postJson('api/companies/', $payload);

        $response
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'user_id' => $user->id,
                    'name' => self::TEST_COMPANY_NAME,
                    'state' => self::TEST_STATE_ISO,
                    'registered_agent_id' => $registeredAgent->id,
                    'registered_agent_type' => RegisteredAgentType::REGISTERED_AGENT->value,
                    'id' => $response['data']['id'],
                ]
            ]);
    }

    public function test_user_can_create_company_with_self_as_agent(): void
    {
        $this->createDummyState();
        $user = User::factory()->create();
        $this->actingAs($user);
        $payload = [
            'user_id' => $user->id,
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO,
            'self_assigned' => true
        ];

        $response = $this->postJson('api/companies', $payload);

        $response
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'user_id' => $user->id,
                    'name' => self::TEST_COMPANY_NAME,
                    'state' => self::TEST_STATE_ISO,
                    'registered_agent_id' => $user->id,
                    'registered_agent_type' => RegisteredAgentType::USER->value,
                    'id' => $response['data']['id'],
                ]
            ]);
    }

    public function test_fallback_to_self_when_state_is_capacity(): void
    {
        $user = User::factory()->create();
        RegisteredAgent::factory()->create([
            'state' => 'IL',
            'capacity' => 1
        ]);
        $this->actingAs($user);
        $payload = [
            'user_id' => $user->id,
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO_FALLBACK,
            'self_assigned' => null
        ];

        $response = $this->postJson('api/companies', $payload);

        $response
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'user_id' => $user->id,
                    'name' => self::TEST_COMPANY_NAME,
                    'state' => self::TEST_STATE_ISO_FALLBACK,
                    'registered_agent_id' => $user->id,
                    'registered_agent_type' => RegisteredAgentType::USER->value,
                    'id' => $response['data']['id'],
                ],
                'meta' => $response['meta']
            ]);
    }

    private function createDummyState(): void {
        State::factory()->create([
            'iso_code' => self::TEST_STATE_ISO,
            'name' => self::TEST_STATE_NAME
        ]);
    }
}
