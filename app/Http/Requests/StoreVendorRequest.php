<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreVendorRequest extends FormRequest
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
            'name'      => 'required|string|max:255|unique:vendors,name',
            'email'     => 'required|email|unique:vendors,email',
            'address'   => 'nullable|string',
            'tax_id'    => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean'
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'name.unique' => 'Name vendor sudah terpakai, silakan gunakan nama lain.',
            'email.unique' => 'Email vendor sudah terpakai, silakan gunakan email lain.',
        ];
    }
}
