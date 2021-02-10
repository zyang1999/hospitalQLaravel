<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\AppointmentFeedback;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\FCMCloudMessaging;

class AppointmentController extends Controller
{
    public function __construct(FCMCloudMessaging $FCMCloudMessaging)
    {
        $this->FCMCloudMessaging = $FCMCloudMessaging;
    }

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

            $token = $appointment->patient->fcm_token;
            $title = 'Appointment';
            $body = 'DR. ' . $request->user()->full_name . ' has cancalled your appointment.';
            $data = [
                'tab' => 'HistoryStack', 
                'screen'=>'AppointmentDetails',
                'appointmentId' => $appointment->id
            ];

            $this->FCMCloudMessaging->sendFCM($token, $title, $body, $data);

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
        $appointment->concern = $request->concern;
        $request->user()->appointments()->save($appointment);

        $token = $appointment->doctor->fcm_token;
        $title = 'Appointment';
        $body = $request->user()->full_name . ' has booked an appointment from you.';
        $data = [
            'type' => 'AppointmentBooking',
            'appointmentId' => $appointment->id
        ];
        
        $this->FCMCloudMessaging->sendFCM($token, $title, $body, $data);

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

    public function getAppointmentView(Request $request){
        $specialties = Specialty::where('specialty', 'not like', 'Phamarcist')->pluck('specialty')->unique();
        $doctors = User::where('role', 'DOCTOR')->get();

        return view('appointment', [
            'specialties' => $specialties,
            'doctors' => $doctors
        ]);
    }

    public function getAppointmentTable(Request $request){
        $appointments = User::find($request->doctorId)->doctorAppointments()
                            ->where('status', 'AVAILABLE')
                            ->whereDate('date', '>', Carbon::today())
                            ->get();
        return view('/components/appointment-table', ['appointments' => $appointments]);
    }

    public function createAppointmentWeb(Request $request){
    
        $validator = Validator::make($request->all(),[
            'first_name' => "regex:/^[a-z ,.'-]+$/i",
            'last_name' => "regex:/^[a-z ,.'-]+$/i",
            'telephone' => 'digits_between:10,11',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ]);
        }

        if($request->patientId == null){
            $user = User::create($request->all());
        }else{
            $user = User::find($request->patientId);
        }

        $appointment = Appointment::find($request->appointmentId);
        $appointment->status = 'BOOKED';
        $appointment->concern = $request->concern;
        $user->appointments()->save($appointment);

        return response()->json([
            'success' => true,
            'message' => 'Appointment is booked successfully!'
        ]);
    }
}
