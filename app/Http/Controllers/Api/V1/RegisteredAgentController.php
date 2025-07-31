<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegisteredAgentCollection;
use App\Services\RegisteredAgentService;

class RegisteredAgentController extends Controller
{
    /**
     * @param string $iso_code
     * @param RegisteredAgentService $registeredAgentService
     * @return RegisteredAgentCollection
     */
    public function verifyCapacity(
        string $iso_code,
        RegisteredAgentService $registeredAgentService
    ): RegisteredAgentCollection
    {
        $registeredAgents = $registeredAgentService->verifyCapacity($iso_code);
        $registeredAgentCollection = new RegisteredAgentCollection($registeredAgents);
        $registeredAgentCollection->additional(['state' => $iso_code]);

        return $registeredAgentCollection;
    }
}
