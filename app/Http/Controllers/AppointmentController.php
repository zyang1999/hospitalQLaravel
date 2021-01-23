<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function createAppointment(){

    }

    public function getAppointments(Request $request){
        $appointments = Appointment::where('doctor_id', 19)->pluck('date');
        
        return response()->json([
            'appointments' => $appointments
        ]);
    }
}
