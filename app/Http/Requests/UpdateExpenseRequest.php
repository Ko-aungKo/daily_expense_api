<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends FormRequest
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
        return [
            'amount' => ['required', 'numeric', 'gt:0'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) {
                    return $query->whereNull('user_id')
                        ->orWhere('user_id', $this->user()?->id);
                }),
            ],
            'payment_method_id' => [
                'required',
                'integer',
                Rule::exists('payment_methods', 'id')->where(function ($query) {
                    return $query->whereNull('user_id')
                        ->orWhere('user_id', $this->user()?->id);
                }),
            ],
            'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
