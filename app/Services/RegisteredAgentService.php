<?php

namespace App\Services;

use App\Events\StateCapacity;
use App\Models\RegisteredAgent;
use Illuminate\Database\Eloquent\Collection;

class RegisteredAgentService
{
    private const PERCENTAGE_LIMIT_EVENT_TRIGGER = 90;

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

    public static function checkStateAgentCapacity(string $isoCode): void
    {
        $agents = RegisteredAgent::where('state', $isoCode)->get();

        if ($agents->isEmpty()) {
            return;
        }

        $totalCapacity = $agents->sum('capacity');
        $assignedCompanies = $agents->sum(fn ($agent) => $agent->companies()->count());
        $usage = round(($assignedCompanies / $totalCapacity) * 100);
        $remainingCapacity = $totalCapacity - $assignedCompanies;

        if (
            $usage != 100
            && (
                $usage >= self::PERCENTAGE_LIMIT_EVENT_TRIGGER
                || $remainingCapacity === 1
            )
        ) {
            event(
                new StateCapacity(
                    self::PERCENTAGE_LIMIT_EVENT_TRIGGER . "%",
                    env('EMAIL_RECIPIENT_AGENT_SERVICE'),
                    $isoCode
                )
            );
        }
    }
}
