<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;

class QueueController extends Controller
{
    public function joinQueue(Request $request)
    {
        $queue = new Queue;
        $queue_no = (int)Queue::where('location', $request->location)->max('queue_no') + 1;
        $queue->queue_no = sprintf("%04d", $queue_no);
        $queue->status = "WAITING";
        $queue->location = $request->location;

        $request->user()->queues()->save($queue);

        return response()->json([
            'queue' => $queue,
        ]);
    }

    public function updateQueue(Request $request)
    {

        $response = [];

        $prevQueue = Queue::find($request->queue_id);
        $prevQueue->status = "COMPLETED";
        $prevQueue->save();

        $nextQueue = Queue::where('queue_no', $prevQueue->queue_no + 1);
        if ($nextQueue) {
            $nextQueue->status = "SERVING";
            $user = User::find($request->user_id);
            if ($user->role == 'DOCTOR') {
                $room_no = $user->doctor()->room_no;
            } else {
                $counter_no = $user->pharmacist()->counter_no;
            }

            $nextQueue->save();
        }

        return response()->json([
            'success' => true,
            'prev_queue' => $prevQueue,
            'next_queue' => $nextQueue,
            'room_no' => $room_no,
            'counter_no' => $counter_no
        ]);
    }

    public function getUserQueue(Request $request){
        return response()->json([
            'user' => $request->user(),
            'userQueue' => $request->user()->queues()->where('status', 'WAITING')->latest()->first()
        ]);      
    }

    public function getAllQueue(Request $request){
        $role = $request->user()->role;

        if($role == 'patient'){
            $location = 'CONSULTATION';
        }else if($role == 'PHARMACIST'){
            $location = 'PHARMACY';
        }

        $allQueue = Queue::where('location', $location)
            ->where('status', 'SERVING')
            ->orWhere('status', 'WAITING')
            ->whereDate('created_at', Carbon::today())
            ->get();
        return response()->json([
            'queue' => $allQueue,
            'patient' => $allQueue->where('status', 'WAITING')->first()->user
        ]);
    }

    public function getCurrentPatient(Request $request){
        $role = $request->user()->role;
        
        if($role == 'DOCTOR'){
            $location = 'CONSULTATION';
        }else if($role == 'PHARMACIST'){
            $location = 'PHARMACY';
        }

        $currentPatient = Queue::where('status', 'SERVING')
            ->where('location', $location)
            ->where('doctor_id', $request->user()->id)
            ->first();

        return response()->json([
            'currentPatient' => $currentPatient
        ]);
    }
}
