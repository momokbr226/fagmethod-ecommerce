<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address_id.required' => 'L\'adresse de livraison est obligatoire',
            'shipping_address_id.exists' => 'L\'adresse de livraison n\'existe pas',
            'billing_address_id.required' => 'L\'adresse de facturation est obligatoire',
            'billing_address_id.exists' => 'L\'adresse de facturation n\'existe pas',
            'payment_method.required' => 'Le moyen de paiement est obligatoire',
            'payment_method.in' => 'Le moyen de paiement n\'est pas valide',
            'notes.string' => 'Les notes doivent être du texte',
            'notes.max' => 'Les notes ne doivent pas dépasser 1000 caractères',
        ];
    }
}
