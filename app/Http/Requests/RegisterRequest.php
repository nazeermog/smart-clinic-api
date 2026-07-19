<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(6)],
            'specialty_id' => ['nullable', 'integer', Rule::exists('specialties', 'id')],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'role' => ['nullable', 'string', Rule::in(['doctor', 'patient'])],
        ];
    }
}
