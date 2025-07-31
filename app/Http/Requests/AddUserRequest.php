<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class AddUserRequest extends FormRequest
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
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users',
            'password' => 'string|min:8|confirmed',
            'phone' => 'string|max:15',
            'role' => 'exists:roles,name',
        ];
    }
     public function failedValidation(Validator $validator){
        if($this->is('api/*')){
            $response= ApiResponse::sendResponse(422, 'fail to register, please try again', $validator->errors()->all());
            throw new ValidationException($validator, $response);
        }
    }
}
