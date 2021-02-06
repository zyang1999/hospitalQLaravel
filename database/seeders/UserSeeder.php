<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Specialty;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $users = User::factory()
            ->count(10)
            ->create();

        foreach ($users as $user) {
            if( $user->role == 'DOCTOR'){
                Specialty::factory()->count(1)->for($user)->create();
                Appointment::factory()->count(3)->for($user, 'doctor')->create();
            }
        }   
    }
}
