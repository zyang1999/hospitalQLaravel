<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function createAppointment(Request $request){
        $validator = Validator::make($request->all(),[
            'date' => 'required',
            'specialty' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ]);
        }else{
            $date = Carbon::parse($request->date)->setTimeZone('Asia/Kuala_Lumpur');
            $dateString = $date->toDateString();
            $startAt = $date->toTimeString();
            $endAt = $date->addMinutes(30)->toTimeString();
            
            $duplicate = $request->user()->doctorAppointments()
                            ->where('date', $dateString)   
                            ->where('start_at', $startAt)
                            ->where('specialty', $request->specialty)
                            ->get();

            if($duplicate->count() != 0){
                return response()->json([
                    'sucesss' => false,
                    'message' => ['error' => 'Duplicated Appointment Found!']
                ]);
            }

            $appointment = $request->user()->doctorAppointments()->create([
                'date'=> $dateString,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'specialty' => $request->specialty,
                'location' => $request->user()->specialties()->where('specialty', $request->specialty)->first()->location,
                'status' => 'AVAILABLE'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment is added successfully',
                'appointment' => $appointment
            ]);
        }
    }

    public function getDoctorAppointments(Request $request){
        $allAppointments = $request->user()->doctorAppointments->load(['patient'])->sortBy('start_at')->groupBy('date');
        
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
