<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class UpdatePrRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-pr') || $this->user()->hasRole('Administrator');;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'                         => 'required|string|max:255',
            'description'                   => 'nullable|string',
            'items'                         => 'required|array|min:1',
            'items.*.item_id'               => 'nullable|exists:items,id',
            'items.*.item_name'             => 'required|string|max:255',
            'items.*.specs'                 => 'nullable|string',
            'items.*.quantity'              => 'required|integer|min:1',
            'items.*.uom'                   => 'required|string',
            'items.*.estimated_unit_price'  => 'required|numeric|min:0'
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'items.required' => 'Daftar barang tidak boleh kosong.',
            'items.min'      => 'Minimal harus ada 1 barang yang diajukan.',
            'items.*.item_name.required' => 'Nama barang wajib diisi.',
            'items.*.quantity.min' => 'Jumlah barang minimal 1.',
        ];
    }
}
