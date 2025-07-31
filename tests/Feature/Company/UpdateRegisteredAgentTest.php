<?php

namespace Tests\Feature\Company;

use App\Enums\RegisteredAgentType;
use App\Models\Company;
use App\Models\RegisteredAgent;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdateRegisteredAgentTest extends TestCase
{
    use DatabaseTransactions;

    private const TEST_COMPANY_NAME = 'Test Co';
    /* There is not a state for DM, but using this for test to prevent touching existing db states */
    private const TEST_STATE_ISO = 'DM';
    private const TEST_STATE_NAME = 'Dummy State';
    private const TEST_STATE_ISO_FALLBACK = 'IL';

    public function test_user_can_update_company_with_registered_agent(): void
    {
        $this->createDummyState();
        $user = User::factory()->create();
        $registeredAgent = RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 3
        ]);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO
        ]);

        $this->actingAs($user);
        $payload = [
            'user_id' => $user->id,
            'state' => self::TEST_STATE_ISO,
            'agent_id' => $registeredAgent->id,
            'self_assigned' => null

        ];
        $response = $this->putJson($this->generateEndpointUrl($company->id), $payload);

        $response->assertOk();
        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $company->id,
                'user_id' => $user->id,
                'name' => self::TEST_COMPANY_NAME,
                'state' => self::TEST_STATE_ISO,
                'registered_agent_type' => RegisteredAgentType::REGISTERED_AGENT->value,
                'registered_agent_id' => $registeredAgent->id,
            ]);
    }

    public function test_user_can_update_company_to_self(): void
    {
        $this->createDummyState();
        $registeredAgent = RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 3
        ]);
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'registered_agent_id' => $registeredAgent,
            'registered_agent_type' => RegisteredAgentType::REGISTERED_AGENT->value,
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO
        ]);

        $this->actingAs($user);
        $payload = [
            'user_id' => $user->id,
            'state' => self::TEST_STATE_ISO,
            'registered_agent_id' => null,
            'self_assigned' => true
        ];
        $response = $this->putJson($this->generateEndpointUrl($company->id), $payload);

        $response->assertOk();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $company->id,
            'user_id' => $user->id,
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO,
            'registered_agent_id' => $user->id,
            'registered_agent_type' => RegisteredAgentType::USER->value,
        ]);
    }

    public function test_fallback_to_self_when_registered_agent_at_capacity(): void
    {
        $this->createDummyState();
        $user = User::factory()->create();
        $previousRegisteredAgent = RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 3
        ]);
        $registeredAgent = RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO_FALLBACK,
            'capacity' => 1
        ]);
        Company::factory()->create([
            'user_id' => $user->id,
            'state' => self::TEST_STATE_ISO_FALLBACK,
            'registered_agent_id' => $registeredAgent->id
        ]);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO_FALLBACK,
            'registered_agent_id' => $previousRegisteredAgent->id
        ]);

        $this->actingAs($user);
        $payload = [
            'user_id' => $user->id,
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO_FALLBACK,
            'registered_agent_id' => $registeredAgent->id,
            'self_assigned' => false
        ];

        $response = $this->putJson($this->generateEndpointUrl($company->id), $payload);

        $response->assertOk();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $company->id,
            'user_id' => $user->id,
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO_FALLBACK,
            'registered_agent_id' => $user->id,
            'registered_agent_type' => RegisteredAgentType::USER->value,
        ]);
    }

    public function test_update_registered_agent_fails_if_company_does_not_belong_to_user(): void
    {
        $this->createDummyState();
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $registeredAgent = RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 1
        ]);
        $company = Company::factory()->create([
            'user_id' => $userB->id,
            'state' => self::TEST_STATE_ISO,
        ]);

        $this->actingAs($userA);
        $payload = [
            'user_id' => $userA->id,
            'state' => self::TEST_STATE_ISO,
            'agent_id' => $registeredAgent->id,
        ];

        $response = $this->putJson($this->generateEndpointUrl($company->id), $payload);

        $response->assertStatus(404);
    }

    /**
     * @param int $companyId
     * @return string
     */
    private function generateEndpointUrl(int $companyId): string {
        return "api/companies/$companyId/registered-agent";
    }

    private function createDummyState(): void {
        State::factory()->create([
            'iso_code' => self::TEST_STATE_ISO,
            'name' => self::TEST_STATE_NAME
        ]);
    }
}
