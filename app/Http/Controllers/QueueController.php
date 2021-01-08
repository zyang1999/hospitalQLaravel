<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\User;

class QueueController extends Controller
{
    public function joinQueue(Request $request)
    {
        $queue = new Queue;
        $queue_no = (int)Queue::where('location', $request->location)->max('queue_no') + 1;
        $queue->queue_no = sprintf("%04d", $queue_no);
        $queue->status = "WAITING";
        $queue->location = "OUTPATIENT";

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
            'userQueue' => $request->user()->queues()->latest()->first()
        ]);      
    }

    public function getAllQueue(Request $request){
        $allQueue = Queue::where('location', 'CONSULTATION')
            ->where('status', 'SERVING')
            ->orWhere('status', 'WAITING')
            ->where('created_at', Carbon::today())
            ->get();
        return response()->json([
            'queue' => $allQueue
        ]);
    }
}
