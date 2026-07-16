<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(['pending', 'confirmed', 'completed', 'cancelled'])],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'The status must be one of: pending, confirmed, completed, cancelled.',
        ];
    }
}
