<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',

            'order_date' => 'required|date',

            'status' => [
                'sometimes',
                Rule::in([
                    'pending',
                    'processing',
                    'completed',
                    'cancelled',
                ]),
            ],

            'items' => 'required|array|min:1',

            'items.*.item_id' => [
                'required',
                'integer',
                'distinct',
                'exists:items,id',
            ],

            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}
