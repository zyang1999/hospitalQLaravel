<?php

namespace Database\Factories;

use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialtyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Specialty::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'specialty' => $this->faker->randomElement($array = array (
                'Family Physician',
                'Internal Medicine Physician',
                'Pediatrician',
                'Obstrtrician',
                'Cardiologies',
                'Dermatologist',
                'Ophthalmologist',
                'Neurologist',
                'Radiologist'
                ))
        ];
    }
}
