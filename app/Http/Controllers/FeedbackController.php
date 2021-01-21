<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Reason;
use App\Models\Queue;

class FeedbackController extends Controller
{
    public function storeFeedback(Request $request){
        $message = null;
        $validator = Validator::make($request->all(),[           
            'queueId' =>'required',
            'feedback' => 'required'
        ]);

        if($validator->fails()){
            $message = [
                'success' => false,
                'message' => $validator->messages()
            ];
        }else{
            $queue = Queue::find($request->queueId);

            $reason = new Reason;
            $reason->reason = $request->feedback;
            $queue->reason()->save($reason);
            $message = [
                'success' =>true
            ];
        }
        return response()->json($message);
    }
}
