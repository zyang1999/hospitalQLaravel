<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Feedback;
use App\Models\Queue;

class FeedbackController extends Controller
{
    public function storeFeedback(Request $request)
    {
        $message = null;
        $validator = Validator::make($request->all(), [
            "queueId" => "required",
            "feedback" => "required|min: ",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->messages(),
            ]);
        }

        $queue = Queue::find($request->queueId);
        $feedback = new Feedback();
        $feedback->feedback = $request->feedback;
        $feedback->created_by = $request->user()->id;
        $queue->feedback()->save($feedback);

        return response()->json([
            "success" => true,
        ]);
    }
}
