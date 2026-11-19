<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['username', 'email', 'contact_no'] as $field) {
            if (is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if (
            isset($data['email']) &&
            $data['email'] === ''
        ) {
            $data['email'] = null;
        }

        if (
            isset($data['contact_no']) &&
            $data['contact_no'] === ''
        ) {
            $data['contact_no'] = null;
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/\A[A-Za-z0-9._-]+\z/',
                Rule::unique('users', 'username'),
            ],

            'email' => [
                'nullable',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'contact_no' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(12)
                    ->max(128)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            // Prevent bypassing separate role APIs
            'name' => ['prohibited'],
            'roles' => ['prohibited'],
            'permissions' => ['prohibited'],
            'role_ids' => ['prohibited'],
            'permission_ids' => ['prohibited'],
        ];
    }
}
