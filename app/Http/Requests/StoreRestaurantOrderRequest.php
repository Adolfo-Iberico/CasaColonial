<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRestaurantOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_type' => ['required', Rule::in(['dine_in', 'takeaway'])],
            'table_id' => [
                Rule::requiredIf(fn (): bool => $this->input('service_type') === 'dine_in'),
                'nullable',
                'integer',
                'exists:tables,id',
            ],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'document_type' => ['nullable', Rule::in(['boleta', 'factura'])],
            'customer_document' => ['nullable', 'digits:11', 'required_if:document_type,factura'],
            'items' => ['required', 'array'],
            'items.*' => ['required', 'integer', 'min:0', 'max:50'],
        ];
    }
}
