<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\User;
use App\Models\Office;
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
        $prevQueue = null;

        $role = $request->user()->role;

        if($request->queue_id){
            $prevQueue = Queue::find($request->queue_id);
            $prevQueue->status = "COMPLETED";
            $prevQueue->save();
            if($role = 'DOCTOR'){
                $queue = new Queue;
                $queue_no = (int)Queue::where('location', 'PHARMACY')->max('queue_no') + 1;
                $queue->queue_no = sprintf("%04d", $queue_no);
                $queue->status = "WAITING";
                $queue->location = 'PHARMACY';
                $queue->user_id = $prevQueue->user_id;
                $queue->save();
            }          
        }

        if($role == 'DOCTOR'){
            $location = 'CONSULTATION';
        }else{
            $location = 'PHARMACY';
        }

        $nextQueue = Queue::where('status','WAITING')->where('location', $location)->first();
        if ($nextQueue) {
            $nextQueue->status = "SERVING";
            $nextQueue->served_by = $request->user()->id; 
            $user = User::find($request->user_id);
            $nextQueue->served_at = $request->user()->office->office_no;
            $nextQueue->save();
        }

        return response()->json([
            'prev_queue' => $prevQueue,
            'next_queue' => $nextQueue,
            
        ]);
    }

    public function getUserQueue(Request $request){
        
        $allQueue = null;

        $userQueue = $request->user()->queues()->where('status', 'WAITING')->orwhere('status', 'SERVING')->latest()->first();
        
        if($userQueue){
            $allQueue = Queue::where('location', $request->location)
            ->where('status', 'SERVING')
            ->orWhere('status', 'WAITING')
            ->whereDate('created_at', Carbon::today())
            ->get();
        }
        
        return response()->json([
            'user' => $request->user(),
            'userQueue' => $userQueue,
            'allQueue' => $allQueue
        ]);      
    }

    public function getAllQueue(Request $request){
        $role = $request->user()->role;

        if($role == 'DOCTOR'){
            $location = 'CONSULTATION';
        }else if($role == 'PHARMACIST'){
            $location = 'PHARMACY';
        }else{
            $location = $request->user()->queues()->where('status', 'WAITING')->latest()->first()->location;
        }

        $allQueue = Queue::where('location', $location)
            ->where('status', 'SERVING')
            ->orWhere('status', 'WAITING')
            ->whereDate('created_at', Carbon::today())
            ->take(4)
            ->get();

        $currentQueue = Queue::where('location', $location)
            ->where('status', 'SERVING')
            ->where('served_by', $request->user()->id)
            ->with('user')
            ->whereDate('created_at', Carbon::today())
            ->first();    

        return response()->json([
            'allQueue' => $allQueue,
            'currentQueue' => $currentQueue
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
