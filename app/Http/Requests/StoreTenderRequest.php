<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTenderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('Administrator') || $this->user()->can('manage-tenders');
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
            'title'                     => 'required|string|max:255',
            'desctiption'               => 'nullable|string',
            'start_date'                => 'required|date|after_or_equal:today',
            'end_date'                  => 'required|date|after:start_date'
        ];
    }
}
