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
use App\Services\FCMCloudMessaging;

class QueueController extends Controller
{
    protected $FCMCloudMessaging;

    public function __construct(FCMCloudMessaging $FCMCloudMessaging)
    {
        $this->FCMCloudMessaging = $FCMCloudMessaging;
    }

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
        $queue->concern = $request->concern;

        $request->user()->queues()->save($queue);
        
        $title = 'New Patient';
        $body =  $request->user()->full_name.' has joined the queue';
        $data = [
            'type' => 'refreshQueue'
        ];

        $this->FCMCloudMessaging->sendFCM($doctor->fcm_token, $title, $body, $data);

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
                $queue->user_id = $prevQueue->user_id;
                $queue->save();
            }
        }

        if($role == 'DOCTOR'){
            $nextQueue = $request->user()->getDoctorPendingQueues()->where('status', 'WAITING')->first()->makeHidden('doctor');
            $nextPatient = $request->user()->getDoctorPendingQueues()->where('status', 'WAITING')->skip(1)->take(1)->first();
        }else if($role == 'NURSE'){
            $nextQueue = $request->user()->getNursePendingQueues()->where('status', 'WAITING')->first()->makeHidden('doctor');
            $nextPatient = $request->user()->getNursePendingQueues()->where('status', 'WAITING')->skip(1)->take(1)->first();
            if($nextQueue != null){
                $nextQueue->served_by = $request->user()->id;
                $nextQueue->location = $request->user()->specialty->location;
            }      
        }

        if ($nextQueue != null) {
            $nextQueue->status = "SERVING";
            $waitingTime = Carbon::parse($nextQueue->created_at)->diffInSeconds(Carbon::now());
            $nextQueue->waiting_time = $waitingTime;
            $nextQueue->save();

            $token = $nextQueue->patient->fcm_token;
            $title = 'Queue Status';
            $body = 'Is your turn now! Please meet your doctor at Room'. $nextQueue->location;
            $data = [
                'type' => 'refreshQueue'
            ];

            $this->FCMCloudMessaging->sendFCM($token, $title, $body, $data);
        }

        if ($nextPatient != null){
            $token = $nextPatient->patient->fcm_token;
            $title = 'Queue Status';
            $body = '1 patient until your turn, please get ready!';
            $data = [
                'type' => 'refreshQueue'
            ];
    
            $this->FCMCloudMessaging->sendFCM($nextPatientToken, $title, $body, $data);
        } 
    
        

        return response()->json([
            'prev_queue' => $prevQueue,
            'next_queue' => $nextQueue
        ]);
    }

    public function getUserQueue(Request $request)
    {
        $allQueue = null;

        $userQueue = $request->user()->queues()
                        ->where('status', 'WAITING')
                        ->orwhere('status', 'SERVING')
                        ->latest()->first();
        if($userQueue != null){
            $userQueue->append('time_range', 'number_of_patients');
        }
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
                $queue = $user->queues()->latest()->first();
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

    public function getQueueDetails(Request $request){
        return Queue::find($request->queueId)->load(['patient', 'doctor', 'feedback']);
    }
}
