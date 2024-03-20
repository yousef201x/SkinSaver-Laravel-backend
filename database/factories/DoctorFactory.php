<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'phone_number' => $this->faker->unique()->phoneNumber,
            'email' => $this->faker->unique()->email,
            'clinic_address' => $this->faker->address(),
            'schedule' => 'Sunday to thursday from 9 AM to 5 PM',
            'doctor_image' => $this->faker->imageUrl,
        ];
    }
}
