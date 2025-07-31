<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateRegisteredAgentRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\RegisteredAgentResource;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * @param StoreCompanyRequest $storeCompanyRequest
     * @param CompanyService $companyService
     * @return CompanyResource
     */
    public function store(StoreCompanyRequest $storeCompanyRequest, CompanyService $companyService): CompanyResource {
        $request = $storeCompanyRequest->validated();
        [$company, $fallback] = $companyService->store($request['user_id'], $request);
        $companyResource = new CompanyResource($company);

        if ($fallback) {
            $companyResource->additional([
                'meta' => 'Requested state is at capacity; you’ve been assigned as your own agent.',
            ]);
        }

        return $companyResource;
    }

    /**
     * @param UpdateRegisteredAgentRequest $updateRegisteredAgentRequest
     * @param Company $company
     * @param CompanyService $companyService
     * @return CompanyResource
     */
    public function updateRegisteredAgent(
        UpdateRegisteredAgentRequest $updateRegisteredAgentRequest,
        Company $company,
        CompanyService $companyService
    ): CompanyResource {
        $request = $updateRegisteredAgentRequest->validated();
        [$companyUpdated, $fallback] = $companyService->updateRegisteredAgent(
            $request['user_id'],
            [
                'company_id' => $company->id,
                'iso_code' => $company->state,
                'agent_id' => $request['agent_id'] ?? null,
                'self_assigned' => $request['self_assigned'] ?? null
            ]
        );
        $companyResource = new CompanyResource(
            $companyUpdated
        );

        if ($fallback) {
            $companyResource->additional([
                'meta' => 'Requested agent is at capacity; you’ve been assigned as your own agent.',
            ]);
        }

        return $companyResource;
    }
}
