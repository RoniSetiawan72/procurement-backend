<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequst extends FormRequest
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
        return [
            'name'          => 'required|string|unique:roles,name|max:50',
            'permissions'   => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name'
        ];
    }

    public function message()
    {
        return [
            'name.unique' => 'Nama role sudah terpakai, silakan gunakan nama lain.',
            'permissions.required' => 'Minimal pilih satu hak akses untuk role ini.',
        ];
    }
}
