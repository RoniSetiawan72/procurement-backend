<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('Administrator');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'name'          => 'required|string|max:50|unique:roles,name,' . $role->id,
            'permissions'   => 'required|array|min:1',
            'permissiond.*' => 'exists:permissions,name'
        ];
    }

    public function message()
    {
        return [
            'name.unique' => 'Nama role sudah terpakai, silakan gunakan nama lain.',
            'permissions.required' => 'Minimal pilih satu hak akses untuk role ini.',
            'permissions.*.exists' => 'Terdapat permission yang tidak valid di dalam sistem.'
        ];
    }
}
