<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('Administrator') || $this->user()->can('submit-bid');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tender_id'     => 'required|exists:tenders,id',
            'vendor_id'     => 'required|exists:vendors,id',
            'offered_price' => 'required|numeric|min:0',
            'bid_document'  => 'required|file|mimes:pdf|max:5120',
        ];
    }
}
