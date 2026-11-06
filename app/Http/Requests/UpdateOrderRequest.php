<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
            'customer_id' => [
                $this->user()?->isCustomer() ? 'prohibited' : 'sometimes',
                'required',
                'exists:customers,id',
            ],

            'order_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'status' => [
                'sometimes',
                'required',
                Rule::in([
                    'pending',
                    'processing',
                    'completed',
                    'cancelled',
                ]),
            ],
        ];
    }
}
