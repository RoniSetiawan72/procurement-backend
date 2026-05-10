<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
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
        $departmentId = $this->route('department') ?: $this->route('id');

        return [
            'name'  => 'required|string|max:255,' . $departmentId,
            'code'  => 'required|string|max:10|unique:departments,code'
        ];
    }

    public function message()
    {
        return [
            'code.unique' => 'Kode department sudah terpakai, silakan gunakan kode lain.',
        ];
    }
}
