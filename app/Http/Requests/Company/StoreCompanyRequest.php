<?php

namespace App\Http\Requests\Company;

use App\Models\RegisteredAgent;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', Rule::exists(User::class, 'id')],
            'name' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', Rule::exists(State::class, 'iso_code')],
            'self_assigned' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'state' => strtoupper($this->input('state')),
        ]);
    }
}
