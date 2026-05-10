<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest extends FormRequest
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
        $vendorId = $this->route('vendor') ?: $this->route('id');
        return [
            'name'      => 'required|string|max:255|unique:vendors,name,' . $vendorId,
            'email'     => 'required|email|unique:vendors,email,' . $vendorId,
            'address'   => 'nullable|string',
            'tax_id'    => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Name vendor sudah terpakai, silakan gunakan nama lain.',
            'email.unique' => 'Email vendor sudah terpakai, silakan gunakan email lain.',
        ];
    }
}
