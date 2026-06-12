<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
        $category = $this->route('category');
        $categoryId = is_object($category) ? $category->id : $category;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique per user, ignoring current record
                Rule::unique('categories')->ignore($categoryId)->where(function ($query) {
                    return $query->where('user_id', $this->user()?->id)
                        ->orWhereNull('user_id');
                }),
            ],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{3,6}$/'],
        ];
    }
}
