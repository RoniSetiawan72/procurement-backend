<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreItemRequest extends FormRequest
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
            'item_code'         => 'required|string|max:255|unique:items,item_code',
            'name'              => 'required|string|max:255',
            'category'          => 'required|string|max:100',
            'uom'               => 'required|string|max:20',
            'estimated_price'   => 'required|numeric|min:0'
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'item_code.unique'     => 'Kode item ini sudah ada di dalam katalog.',
            'estimated_price.min'  => 'Harga perkiraan tidak boleh bernilai minus.',
            'estimated_price.numeric' => 'Harga perkiraan harus berupa angka yang valid.'
        ];
    }
}
