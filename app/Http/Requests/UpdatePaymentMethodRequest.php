<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $paymentMethod = $this->route('payment_method');
        $paymentMethodId = is_object($paymentMethod) ? $paymentMethod->id : $paymentMethod;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique per user, ignoring current record
                Rule::unique('payment_methods')->ignore($paymentMethodId)->where(function ($query) {
                    return $query->where('user_id', $this->user()?->id)
                        ->orWhereNull('user_id');
                }),
            ],
        ];
    }
}
