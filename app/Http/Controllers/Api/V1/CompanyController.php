<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Resources\CompanyResource;
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
}
