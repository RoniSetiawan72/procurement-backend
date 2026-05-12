<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('Administrator') || $this->user()->can('create-po');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'purchase_requisition_id'   => 'required|exists:purchase_requisitions,id',
            'vendor_id'                 => 'required|exists:vendors,id',
            'expected_delivery_date'    => 'nullable|date|after_or_equal:today',
            'notes'                     => 'nullable|string',

            'items'                     => 'required|array|min:1',
            'items.*.item_id'           => 'nullable|exists:items,id',
            'items.*.item_name'         => 'required|string|max:255',
            'items.*.quantity'          => 'required|integer|min:1',
            'items.*.uom'               => 'required|string|max:20',
            'items.*.actual_unit_price' => 'required|numeric|min:0'
        ];
    }
}
