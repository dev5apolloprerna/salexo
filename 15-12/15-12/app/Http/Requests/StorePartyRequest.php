<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StorePartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return JSON errors if request is API,
     * otherwise use normal redirect back (web).
     */
    protected function failedValidation(Validator $validator)
    {
        // If request expects JSON or comes from API guard → return JSON
        if ($this->expectsJson() || $this->is('api/*') || auth('employee_api')->check()) {
            throw new HttpResponseException(
                response()->json([
                    'success'=>false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422)
            );
        }

        // Otherwise, default web redirect with errors
        parent::failedValidation($validator);
    }

    public function rules(): array
    {
        // If authenticated via employee API, get company from token
        if (auth('employee_api')->check()) {
            $authCompanyId = auth('employee_api')->user()->company_id;
        } elseif (auth('web_employees')->check()) {
            $authCompanyId = auth('web_employees')->user()->company_id;
        } else {
            $authCompanyId = null;
        }

        $companyId = $authCompanyId ?: (int) $this->input('iCompanyId');

        return [
            // Unique name per company
            'strPartyName' => [
                'required','string','max:255',
                Rule::unique('party','strPartyName')
                    ->where(fn($q) => $q->where('iCompanyId', $companyId)),
            ],

            'address1'         => ['nullable','string','max:255'],
            'address2'         => ['nullable','string','max:255'],
            'city'             => ['required','string','max:255'],
            'state_id'         => ['nullable','string','max:255'],

            // GST must be UNIQUE globally
            'strGST' => [
                'nullable','string','max:15',
                Rule::unique('party','strGST')
                    ->where(fn($q) => $q->whereNotNull('strGST')->where('strGST','!=','')),
            ],

            'iMobile'  => ['nullable','string','max:20'],
            'strEmail' => ['nullable','email','max:255'],

            'strContactPersonName' => ['required','string','max:255'],

            'pincode'       => ['nullable','digits_between:4,10'],
            'strIP'         => ['nullable','ip'],
            'strEntryDate'  => ['required','date'],
            'iStatus'       => ['nullable','integer','in:0,1'],

            // Required only if NOT authenticated via API
            'iCompanyId'    => $authCompanyId ? ['nullable'] : ['required','integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'strPartyName.unique' => 'This party already exists for your company.',
            'strGST.unique'       => 'This GST number already exists for another party.',
            'city.required'       => 'City field is required.',
            'strEntryDate.required' => 'Entry date is required.',
            'iCompanyId.required'   => 'Company ID is required.',
        ];
    }
}
