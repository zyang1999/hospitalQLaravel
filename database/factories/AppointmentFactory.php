<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'start_at' => $this->faker->time,
            'end_at' => $this->faker->time,
            'specialty' => 'Family Physician',
            'location' => 'Room 2',
            'status' => 'AVAILABLE'
        ];
    }
}
