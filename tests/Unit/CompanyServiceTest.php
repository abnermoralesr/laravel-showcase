<?php

namespace Tests\Unit;

use App\Enums\RegisteredAgentType;
use App\Events\RegisteredAgentAssigned;
use App\Events\StateCapacity;
use App\Models\Company;
use App\Models\RegisteredAgent;
use App\Models\User;
use App\Notifications\NewCompanyAssigned;
use App\Services\CompanyService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class CompanyServiceTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    private const TEST_COMPANY_NAME = 'Test Co';
    /* There is not a state for DM, but using this for test to prevent touching existing db states */
    private const TEST_STATE_ISO = 'DM';

    public function test_create_company(): void
    {
        Event::fake();
        Notification::fake();

        $user = User::factory()->create();
        RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 10
        ]);
        $data = [
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO,
            'registered_agent_id' => null,
            'self_assigned' => null
        ];

        $companyService = new CompanyService();
        [$company, $fallback] = $companyService->store($user->id, $data);
        $agent = $company->registered_agent;

        $this->assertNotEquals($user->id, $company->registered_agent_id);
        $this->assertFalse($fallback);
        $this->assertEquals($company->registered_agent_type, RegisteredAgentType::REGISTERED_AGENT);

        Event::assertDispatched(RegisteredAgentAssigned::class);
        Event::assertNotDispatched(StateCapacity::class);
    }

    public function test_create_company_and_assign_user_as_agent(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $data = [
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO,
            'registered_agent_id' => null,
            'self_assigned' => true
        ];

        $companyService = new CompanyService();
        [$company, $fallback] = $companyService->store($user->id, $data);

        $this->assertEquals($user->id, $company->registered_agent_id);
        $this->assertFalse($fallback);

        Event::assertNotDispatched(RegisteredAgentAssigned::class);
        Event::assertNotDispatched(StateCapacity::class);
    }

    public function test_fallback_to_self_assigned_when_no_capacity(): void
    {
        Event::fake();

        $user = User::factory()->create();
        RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 0
        ]);
        $data = [
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO,
            'registered_agent_id' => null,
            'self_assigned' => null
        ];

        $companyService = new CompanyService();
        [$company, $fallback] = $companyService->store($user->id, $data);

        $this->assertEquals($user->id, $company->registered_agent_id);
        $this->assertTrue($fallback);

        Event::assertNotDispatched(RegisteredAgentAssigned::class);
    }

    public function test_event_state_capacity_when_agent_assigned_to_company(): void
    {
        Notification::fake();
        Event::fake();

        $user = User::factory()->create();
        $registeredAgent = RegisteredAgent::factory()->create([
            'state' => self::TEST_STATE_ISO,
            'capacity' => 4
        ]);

        Company::factory()->count(2)->create([
           'registered_agent_id' => $registeredAgent->id,
           'user_id' => $user->id,
           'state' => self::TEST_STATE_ISO
        ]);
        $data = [
            'name' => self::TEST_COMPANY_NAME,
            'state' => self::TEST_STATE_ISO,
            'registered_agent_id' => $registeredAgent->id,
            'self_assigned' => null
        ];

        $companyService = new CompanyService();
        [$company, $fallback] = $companyService->store($user->id, $data);

        $this->assertNotEquals($user->id, $company->registered_agent_id);
        $this->assertFalse($fallback);

        Event::assertDispatched(StateCapacity::class);
    }
}
