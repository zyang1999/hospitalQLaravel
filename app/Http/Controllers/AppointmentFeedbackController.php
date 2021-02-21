<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AppointmentFeedback;
use App\Models\Appointment;

class AppointmentFeedbackController extends Controller
{
    public function storeAppointmentFeedback(Request $request)
    {
        $message = null;
        $validator = Validator::make($request->all(), [
            "appointmentId" => "required",
            "feedback" => "required",
        ]);

        if ($validator->fails()) {
            $message = [
                "success" => false,
                "message" => $validator->messages(),
            ];
        } else {
            $appointment = Appointment::find($request->appointmentId);

            $feedback = new AppointmentFeedback();
            $feedback->feedback = $request->feedback;
            $feedback->created_by = $request->user()->id;
            $appointment->feedback()->save($feedback);
            $message = [
                "success" => true,
            ];
        }
        return response()->json($message);
    }
}
