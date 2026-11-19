<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['name', 'email', 'contact_no'] as $field) {
            if (is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        foreach (['email', 'contact_no'] as $field) {
            if (
                array_key_exists($field, $data) &&
                $data[$field] === ''
            ) {
                $data[$field] = null;
            }
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        // Supports both /api/users/{user}
        // and the authenticated /api/profile route.
        $user = $this->route('user') ?? $this->user();

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'sometimes',
                'nullable',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'contact_no' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],

            // Immutable and sensitive fields
            'username' => ['prohibited'],
            'password' => ['prohibited'],
            'roles' => ['prohibited'],
            'permissions' => ['prohibited'],
            'role_ids' => ['prohibited'],
            'permission_ids' => ['prohibited'],
            'is_super_admin' => ['prohibited'],
        ];
    }
}
