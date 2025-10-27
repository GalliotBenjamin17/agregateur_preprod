<?php

namespace App\Http\Requests\Api\Donation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDonationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required',
            'projectId' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => ':amount field missing',
            'projectId.required' => ':projectId field missing',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors(),
        ]));
    }

    public function authorize(): bool
    {
        return true;
    }
}
