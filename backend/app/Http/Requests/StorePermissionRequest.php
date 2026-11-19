<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('name'))) {
            $this->merge([
                'name' => strtolower(
                    trim($this->input('name'))
                ),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',

                // Example: tickets.create
                'regex:/\A[a-z][a-z0-9_-]*(?:\.[a-z][a-z0-9_-]*)+\z/',

                Rule::unique('permissions', 'name')
                    ->where('guard_name', 'web'),
            ],
        ];
    }
}
