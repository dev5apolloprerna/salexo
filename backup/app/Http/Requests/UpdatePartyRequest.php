<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->is('api/*') || auth('employee_api')->check()) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    public function rules(): array
    {
        $partyId = (int) $this->input('party_id');
       

        // detect company ID
        if (auth('employee_api')->check()) {
            $companyId = auth('employee_api')->user()->company_id;
        } elseif (auth('web_employees')->check()) {
            $companyId = auth('web_employees')->user()->company_id;
        } else {
            $companyId = (int) $this->input('iCompanyId');
        }

        return [
            'strPartyName' => [
                'required','string','max:255',
                Rule::unique('party','strPartyName')
                    ->where(fn($q) => $q->where('iCompanyId', $companyId))
                    ->ignore($partyId, 'partyId'),
            ],

            'address1' => ['nullable','string','max:255'],
            'address2' => ['nullable','string','max:255'],
            'city'     => ['required','string','max:255'],
            'state_id' => ['nullable','string','max:255'],

            // ✅ GST must be unique across all parties EXCEPT this party
            'strGST' => [
                'nullable','string','max:15',
                Rule::unique('party','strGST')
                    ->where(fn($q) => $q->whereNotNull('strGST')->where('strGST','!=',''))
                    ->ignore($partyId, 'partyId'),
            ],

            'iMobile'  => ['nullable','string','max:20'],
            'strEmail' => ['nullable','email','max:255'],
            'strContactPersonName' => ['required','string','max:255'],
            'pincode'      => ['nullable','digits_between:4,10'],
            'strIP'        => ['nullable','ip'],
            'strEntryDate' => ['required','date'],
            'iStatus'      => ['nullable','integer','in:0,1'],

            'iCompanyId'   => $companyId ? ['nullable'] : ['required','integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'strPartyName.unique' => 'This party name already exists for your company.',
            'strGST.unique'       => 'This GST number already exists for another party.',
            'city.required'       => 'City field is required.',
            'strContactPersonName.required' => 'Contact person name is required.',
            'strEntryDate.required'=> 'Entry date is required.',
        ];
    }
}
