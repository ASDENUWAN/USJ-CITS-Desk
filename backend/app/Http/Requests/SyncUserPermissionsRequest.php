<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncUserPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'permission_ids' => [
                'present',
                'array',
                'max:200',
            ],

            'permission_ids.*' => [
                'required',
                'integer',
                'distinct',

                Rule::exists('permissions', 'id')
                    ->where('guard_name', 'web'),
            ],
        ];
    }
}
