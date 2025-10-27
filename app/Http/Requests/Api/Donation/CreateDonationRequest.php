<?php

namespace App\Http\Requests\Api\Donation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateDonationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'projectId' => 'nullable',
            'wpPageId' => 'required',
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
