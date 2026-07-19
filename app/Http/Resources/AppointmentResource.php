<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'patient_full_name' => $this->patient?->full_name,
            'patient_email' => $this->patient?->email,
            'doctor_full_name' => $this->doctor?->user?->full_name,
            'doctor_email' => $this->doctor?->user?->email,
            'patient' => PatientResource::make($this->whenLoaded('patient')),
            'doctor' => DoctorResource::make($this->whenLoaded('doctor')),
            'appointment_date' => $this->appointment_date?->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'doctor_notes' => $this->doctor_notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
