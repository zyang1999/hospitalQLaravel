<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function createAppointment(){

    }

    public function getDoctorAppointments(Request $request){
        $allAppointments = $request->user()->doctorAppointments->load(['patient'])->groupBy('date');
        
        return response()->json([
            'allAppointments' => $allAppointments
        ]);
    }

    public function getAvailableDate(Request $request){
        $appointments = Appointment::where('doctor_id', $request->doctorId)
            ->whereDate('date', '>' , Carbon::today())
            ->pluck('date');
        
        return response()->json([
            'appointments' => $appointments
        ]);
    }

    public function getSchedule(Request $request){
        $scheuldes = Appointment::where('doctor_id', $request->doctorId)->where('date', $request->date)->get();

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
        $appointments = $request->user()->appointments()->where('status', 'BOOKED')->oldest('date')->get()->load(['user.specialty']);
        $appointmentToday = $request->user()->appointments()->where('status', 'BOOKED')->whereDate('date', Carbon::today())->first();
        
        if($appointmentToday){
            $appointmentToday = $appointmentToday->load(['user.specialty']);
        }
        
        return response()->json([
            'appointmentToday' => $appointmentToday,
            'appointments' => $appointments
        ]);
    }
}
