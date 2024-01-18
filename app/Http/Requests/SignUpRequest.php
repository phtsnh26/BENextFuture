<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignUpRequest extends FormRequest
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
    public function rules()
    {
        return [
            'username' => [
                'required',
                'string',
                Rule::unique('clients', 'username')->ignore($this->route('client')),
            ],
            'password' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('clients', 'email')->ignore($this->route('client')),
            ],
            'phone_number' => 'required|digits:10|string',
            'fullname' => 'required|string',
            'date_of_birth' => 'required|date',
            'avatar' => 'nullable|string',
            'status' => 'integer|in:0,1',
            'gender' => 'required|in:1,0,-1',
            'nickname' => 'nullable|string',
            'address' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Please enter the username.',
            'username.unique' => 'The username has already been taken.',
            'password.required' => 'Please enter the password.',
            'email.required' => 'Please enter the email address.',
            'email.email' => 'The email address is invalid.',
            'email.unique' => 'The email address has already been taken.',
            'phone_number.required' => 'Please enter the 10-digit phone number.',
            'phone_number.digits' => 'The phone number must be exactly 10 digits.',
            'fullname.required' => 'Please enter the full name.',
            'date_of_birth.required' => 'Please enter the date of birth.',
            'date_of_birth.date' => 'Date of birth must be in date format.',
            'status.integer' => 'Status must be an integer.',
            'status.in' => 'Invalid status.',
            'gender.required' => 'Please choose the gender.',
            'gender.in' => 'Invalid gender.',
        ];
    }
}
