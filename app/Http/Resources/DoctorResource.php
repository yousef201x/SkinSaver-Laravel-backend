<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'doctor name' => $this->name,
            'phone number' => $this->phone_number,
            'doctor email' => $this->email,
            'doctor clinic address' => $this->clinic_address,
            'image' => asset('storage/' . $this->doctor_image),
            'doctor schedule' => $this->schedule,
        ];
    }
}
