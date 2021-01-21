<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'IC_no' => $this->faker->randomNumber,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'IC_image' => $this->faker->imageUrl,
            'selfie' => $this->faker->imageUrl,
            'telephone' => $this->faker->phoneNumber,
            'role' => $this->faker->randomElement($array = array('PATIENT', 'DOCTOR', 'NURSE')),
            'password' => Hash::make('testing'), // password
            'remember_token' => Str::random(10),
        ];
    }
}
