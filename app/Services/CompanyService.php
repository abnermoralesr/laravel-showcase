<?php

namespace App\Services;

use App\Enums\RegisteredAgentType;
use App\Models\Company;
use App\Models\RegisteredAgent;
use App\Models\User;

class CompanyService
{
    private const ISO_CODE_ILLINOIS = 'IL';

    /**
     * @param int $userId
     * @param array $data
     * @return array
     */
    public function store(int $userId, array $data): array
    {
        $user = User::findOrFail($userId);
        [$agentId, $registeredType, $fallback] = $this->prepareRegisteredAgent(
            $user->id,
            $data['state'],
            $data['self_assigned'] ?? null,
            $data['agent_id'] ?? null,
        );

        $company = Company::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'state' => $data['state'],
            'registered_agent_id' => $agentId,
            'registered_agent_type' => $registeredType,
        ]);

        return [
            $company,
            $fallback
        ];
    }

    /**
     * @param int $userId
     * @param string $isoCode
     * @param bool|null $selfAssigned
     * @param int|null $registeredAgentId
     * @return array
     */
    private function prepareRegisteredAgent(int $userId, string $isoCode, ?bool $selfAssigned = null, ?int $registeredAgentId = null): array
    {
        $agentId = $userId;
        $registeredType = RegisteredAgentType::USER;

        if ($selfAssigned) {
            return [
                $agentId,
                $registeredType,
                false
            ];
        }

        if ($agent = $this->getAvailableAgent($isoCode, $registeredAgentId)) {
            $agentId = $agent->id;
            $registeredType = RegisteredAgentType::REGISTERED_AGENT;
        }

        $fallback = $registeredType == RegisteredAgentType::USER;

        return [
            $agentId,
            $registeredType,
            $fallback
        ];
    }

    /**
     * @param string $isoCode
     * @param int|null $agentId
     * @return RegisteredAgent|null
     */
    private function getAvailableAgent(string $isoCode, ?int $agentId): ?RegisteredAgent
    {
        if ($isoCode === self::ISO_CODE_ILLINOIS) {
            return null;
        }

        $registeredAgentQuery = RegisteredAgent::query();
        $registeredAgentQuery->withCount('companies');
        $registeredAgentQuery->where('state', $isoCode);

        if ($agentId) {
            $registeredAgentQuery->where('id', $agentId);
        }

        $registeredAgents = $registeredAgentQuery->get()->filter(function ($agent) {
            return $agent->companies_count < $agent->capacity;
        });

        if ($registeredAgents->isEmpty()) {
            return null;
        }

        $sortedAgents = $registeredAgents->sortBy(function ($agent) {
            return $agent->companies_count;
        });

        return $sortedAgents->first();
    }
}
