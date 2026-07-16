<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
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
