<?php

namespace App\Services;

use App\Models\RegisteredAgent;

class RegisteredAgentService
{
    /**
     * @param string $isoCode
     * @return array|null
     */
    public function verifyCapacity(string $isoCode): ?Collection
    {
        return RegisteredAgent::where('state', $isoCode)
            ->withCount('companies')
            ->get()
            ->filter(fn($agent) => $agent->companies_count < $agent->capacity);
    }
}
