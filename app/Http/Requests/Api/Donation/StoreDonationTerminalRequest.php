<?php

namespace App\Http\Requests\Api\Donation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDonationTerminalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user.email' => 'required|email',
            'user.telephone' => 'required|string',
            'user.AcceptRegisterData' => 'required|boolean',
            'user.AcceptCGV' => 'required|boolean',
            'user.AcceptEmailing' => 'required|boolean',
            'paiement.Montant' => 'required|numeric',
            'paiement.active_carbon_price' => 'required',
            'paiement.NumTransaction' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'user.email.required' => 'L\'adresse e-mail est requise.',
            'user.email.email' => 'L\'adresse e-mail doit être valide.',
            'user.telephone.required' => 'Le numéro de téléphone est requis.',
            'user.telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'user.AcceptRegisterData.required' => 'L\'acceptation des données d\'inscription est requise.',
            'user.AcceptRegisterData.boolean' => 'L\'acceptation des données d\'inscription doit être un booléen.',
            'user.AcceptCGV.required' => 'L\'acceptation des CGV est requise.',
            'user.AcceptCGV.boolean' => 'L\'acceptation des CGV doit être un booléen.',
            'user.AcceptEmailing.required' => 'L\'acceptation de l\'envoi d\'e-mails est requise.',
            'user.AcceptEmailing.boolean' => 'L\'acceptation de l\'envoi d\'e-mails doit être un booléen.',
            'paiement.Montant.required' => 'Le montant est requis.',
            'paiement.Montant.numeric' => 'Le montant doit être un nombre.',
            'paiement.active_carbon_price.required' => 'Le prix du carbone actif est requis.',
            'paiement.NumTransaction.required' => 'Le numéro de transaction est requis.',
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
