<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique per user (and unique among system defaults where user_id is null)
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('user_id', $this->user()?->id)
                        ->orWhereNull('user_id');
                }),
            ],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{3,6}$/'],
        ];
    }
}
