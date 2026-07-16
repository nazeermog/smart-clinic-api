<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->whenLoaded('user', fn () => $this->user->full_name),
            'email' => $this->whenLoaded('user', fn () => $this->user->email),
            'consultation_fee' => $this->consultation_fee,
            'specialty' => SpecialtyResource::make($this->whenLoaded('specialty')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
