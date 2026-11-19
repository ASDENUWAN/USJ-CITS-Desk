<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('name'))) {
            $this->merge([
                'name' => trim($this->input('name')),
            ]);
        }
    }

    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:80',
                'regex:/\A[A-Za-z][A-Za-z0-9 ._-]*\z/',

                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($role?->id),

                function ($attribute, $value, $fail) {
                    if (
                        is_string($value) &&
                        strcasecmp($value, 'Super Admin') === 0
                    ) {
                        $fail(
                            'Super Admin is a reserved role name.'
                        );
                    }
                },
            ],
        ];
    }
}
