<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             "amount_cents" => "4000",
                "currency" => "EGP",
                "shipping_data" => [
                    "first_name" => "Test",
                    "last_name" => "Account",
                    "phone_number" => "01010101010",
                    "email" => "test@account.com"
                ],

                "items" => [

                    "name" => "ASC1525",
                    "amount_cents" => "4000",
                    "quantity" => "1",
                    "description" => "Smart Watch"

                ],
                "delivery_needed" => "false"
        ];
    }
}
