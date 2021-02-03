<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Queue;
use App\Models\User;
use App\Models\Office;
use App\Models\Feedback;
use App\Models\Specialty;
use Carbon\Carbon;

class QueueController extends Controller
{
    public function joinQueue(Request $request)
    {

        $doctorId = Specialty::where('specialty', $request->specialty)
                        ->get()
                        ->sortBy(function ($queue){
                            return count($queue->user->staffQueues);
                        })
                        ->pluck('user.id')
                        ->values()
                        ->all();
        
        $doctor = User::find($doctorId[0]);
        $location = $doctor->specialty->location;

        $queue = new Queue;
        $queue_no = (int)Queue::where('specialty', $request->specialty)
                        ->whereDate('created_at', Carbon::today())
                        ->max('queue_no') + 1;
        $queue->queue_no = sprintf("%04d", $queue_no);
        $queue->status = "WAITING";
        $queue->location = $location;
        $queue->served_by = $doctor->id;
        $queue->specialty = $request->specialty;

        $request->user()->queues()->save($queue);

        return response()->json([
            'queue' => $queue
        ]);
    }

    public function updateQueue(Request $request)
    {
        $prevQueue = null;

        $role = $request->user()->role;

        if ($request->queue_id) {
            $prevQueue = Queue::find($request->queue_id);
            $prevQueue->status = "COMPLETED";
            $prevQueue->save();
            if ($role == 'DOCTOR') {
                $queue = new Queue;
                $queue_no = (int)Queue::where('specialty', 'Phamarcy')
                                ->whereDate('created_at', Carbon::today())
                                ->max('queue_no') + 1;
                $queue->queue_no = sprintf("%04d", $queue_no);
                $queue->status = "WAITING";
                $queue->specialty = 'Phamarcy';
                $queue->patient_id = $prevQueue->patient_id;
                $queue->save();
            }
        }

        if($role == 'DOCTOR'){
            $nextQueue = $request->user()->getDoctorPendingQueues()->where('status', 'WAITING')->first();
        }else{
            $nextQueue = $request->user()->getNursePendingQueues()->where('status', 'WAITING')->first();
            if($nextQueue != null){
                $nextQueue->served_by = $request->user()->id;
                $nextQueue->location = $request->user()->specialty->location;
            }      
        }

        if ($nextQueue != null) {
            $nextQueue->status = "SERVING";
            $nextQueue->save();
        }

        return response()->json([
            'prev_queue' => $prevQueue,
            'next_queue' => $nextQueue,

        ]);
    }

    public function getUserQueue(Request $request)
    {
        $allQueue = null;

        $userQueue = $request->user()->queues()
                        ->where('status', 'WAITING')
                        ->orwhere('status', 'SERVING')
                        ->latest()->first();

        return response()->json([
            'user' => $request->user(),
            'userQueue' => $userQueue
        ]);
    }

    public function getAllQueue(Request $request)
    {
        $allQueue = [];
        $currentQueue = [];
        $user = $request->user();

        switch ($user->role) {
            case 'PATIENT':
                $queue = $user->queues()->where('status', 'WAITING')->latest()->first();
                if($queue->specialty == 'Phamarcy'){
                    $allQueue = $user->getNursePendingQueues();
                }else{
                    $allQueue = User::find($queue->served_by)->getDoctorPendingQueues();
                }     
                break;

            case 'DOCTOR':
                $allQueue = $user->getDoctorPendingQueues();
                $currentQueue = $user->getCurrentServing();
                break;

            case 'NURSE':
                $allQueue = $user->getNursePendingQueues();
                $currentQueue = $user->getCurrentServing();
                break;
        }

        return response()->json([
            'allQueue' => $allQueue,
            'currentQueue' => $currentQueue
        ]);
    }

    public function getAverageWaitingTime(Request $request){
        $queue = Queue::where('location', $request->specialty)->where('status', 'WAITING')->get();
        $numberOfPatients = $queue->count();
        $averageWaitingTime = $queue->avg('waiting_time');
        $totalWaitingSeconds = $numberOfPatients * $averageWaitingTime * 60;
        $currentTime = Carbon::now();
        $extimatedServedAt = $currentTime->addSeconds($totalWaitingSeconds);
        $timeRange = $extimatedServedAt->format('h:i A'). ' - ' . $extimatedServedAt->addMinutes(15)->format('h:i A');
        return response()->json([
            'timeRange' => $timeRange
        ]);

    }

    public function getCurrentPatient(Request $request)
    {
        $role = $request->user()->role;

        if ($role == 'DOCTOR') {
            $location = 'CONSULTATION';
        } else if ($role == 'PHARMACIST') {
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

    public function cancelQueue(Request $request)
    {
        $message = ['feedback.required' => 'The reason field is required.'];

        $validator = Validator::make($request->all(),[
            'queueId' => 'required',
            'feedback' => 'required'
        ], $message);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ]);
        }

        $queueId = $request->queueId;

        $queue = Queue::find($queueId);
        $queue->status = 'CANCELLED';
        $queue->save();

        $feedback = new Feedback;
        $feedback->feedback = $request->feedback;
        $queue->feedback()->save($feedback);

        return response()->json([
            'success' => true,
            'message' => 'Queue is cancelled successfully',
            'queue' => $queue,
            'feedback' => $feedback
        ]);
    }

    public function getQueueHistory(Request $request){
        $queueHistory = $request->user()->queues->diff(Queue::where('status', 'WAITING')->get())->load(['feedback', 'served_by']);
        return response()->json([
            'queueHistory' => $queueHistory
        ]);
    }
}
