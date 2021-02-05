<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\AppointmentFeedback;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function createAppointment(Request $request){
        $date = Carbon::parse($request->date)->setTimeZone('Asia/Kuala_Lumpur');
        $dateString = $date->toDateString();
        $startAt = $date->toTimeString();
        $endAt = $date->addMinutes(30)->toTimeString();
        $specialty = $request->user()->specialty;
        
        $duplicate = $request->user()->doctorAppointments()
                        ->where('date', $dateString)   
                        ->where('start_at', $startAt)
                        ->where('specialty', $specialty->specialty)
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
            'specialty' => $specialty->specialty,
            'location' => $specialty->location,
            'status' => 'AVAILABLE'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment is added successfully',
            'appointment' => $appointment
        ]);
    }

    public function getDoctorAppointmentsToday(Request $request){
        $appointments = $request->user()->doctorAppointments()->whereDate('date', Carbon::today())->get()->load(['patient', 'feedback','feedback']);

        return response()->json([
            'appointments' =>$appointments
        ]);
    }

    public function completeAppointment (Request $request){
        $appointment = Appointment::find($request->id);
        $appointment->status = 'COMPLETED';
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully'
        ]);
    }

    public function deleteAppointment(Request $request){
        $appointment = Appointment::find($request->id);
        if($appointment->patient_id !== null){

            $message = ['feedback.required' => 'The reason field is required.'];

            $validator = Validator::make($request->all(),[
                'feedback' => 'required'
            ], $message);
                
            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'message' => $validator->messages()
                ]);
            }

            $appointment->status = 'CANCELLED';
            $appointment->save();
            
            $feedback = new AppointmentFeedback;
            $feedback->feedback = $request->feedback;
            $appointment->feedback()->save($feedback);
        }else{  
            $appointment->delete();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Appointment is deleted successfully'
        ]);
    }

    public function getDoctorAppointments(Request $request){
        $allAppointments = $request->user()->doctorAppointments->makeHidden(['doctor', 'patient'])
                            ->sortBy('start_at')
                            ->groupBy(function ($item){
                                return($item->date->format('Y-m-d'));
                            });
        
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
        $appointments = $request->user()->appointments()->where('status', 'BOOKED')->oldest('date')->get()->load(['doctor']);
        $appointmentToday = $request->user()->appointments()->where('status', 'BOOKED')->whereDate('date', Carbon::today())->first();
        
        return response()->json([
            'appointmentToday' => $appointmentToday,
            'appointments' => $appointments
        ]);
    }

    public function getAppointmentDetails (Request $request){
        return Appointment::find($request->appointmentId)->load(['patient', 'feedback', 'doctor']);
    }
}
