<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function createAppointment(){

    }

    public function getAvailableDate(Request $request){
        $appointments = Appointment::where('doctor_id', 19)->pluck('date');
        
        return response()->json([
            'appointments' => $appointments
        ]);
    }

    public function getSchedule(Request $request){
        $scheuldes = Appointment::where('doctor_id', 19)->where('date', $request->date)->get();

        return response()->json([
            'schedules' => $scheuldes
        ]);
    }

    public function bookAppointment(Request $request){
        $appointment = Appointment::find($request->appointmentId);
        $appointment->status = 'BOOKED';
        $request->user()->appointments()->save($appointment);

        return response()->json([
            'success' => true,
            'appointment' => $appointment
        ]);
    }

    public function getAppointment (Request $request){
        return response()->json([
            'appointments' => $request->user()->appointments->load(['user.specialties'])
        ]);
    }
}
