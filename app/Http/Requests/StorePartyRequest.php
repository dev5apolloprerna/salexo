<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePartyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $companyId = (int)($this->input('iCompanyId'));

        return [
            'strPartyName' => [
                'required','string','max:255',
                Rule::unique('party')->where(fn($q) => $q->where('iCompanyId',$companyId)),
            ],
            'address1'     => ['nullable','string','max:255'],
            'city'     => ['nullable','string','max:255'],
            'state'     => ['nullable','string','max:255'],
            'strGST'       => ['nullable','string','max:15',
                Rule::unique('party')->where(fn($q) => $q->where('iCompanyId',$companyId)),
            ],
            'iMobile'      => ['nullable','string','max:20'],
            'strEmail'     => ['nullable','email','max:255'],
            'strIP'        => ['nullable','ip'],
            'strEntryDate' => ['required','date'],
            'iStatus'      => ['nullable','integer','in:0,1'],
        ];
    }
}
