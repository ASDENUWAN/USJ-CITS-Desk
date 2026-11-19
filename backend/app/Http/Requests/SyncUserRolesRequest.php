<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncUserRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_ids' => [
                'present',
                'array',
                'max:50',
            ],

            'role_ids.*' => [
                'required',
                'integer',
                'distinct',

                Rule::exists('roles', 'id')
                    ->where('guard_name', 'web'),
            ],
        ];
    }
}
