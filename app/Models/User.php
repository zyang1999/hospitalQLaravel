<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $appends = ['selfie_string', 'full_name'];

    public function getSelfieStringAttribute()
    {
        $base64 = null;
        if($this->selfie != null){
            $data = file_get_contents($this->selfie);
            $base64 = 'data:image/png;base64,' . base64_encode($data);
        }      
        return $base64;
    }

    public function queues()
    {
        return $this->hasMany(Queue::class, 'user_id');
    }

    public function office()
    {
        return $this->hasOne(Office::class);
    }

    public function specialty(){
        return $this->hasOne(Specialty::class, 'doctor_id');
    }

    public function appointments(){
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function doctorAppointments(){
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function staffQueues(){
        return $this->hasMany(Queue::class, 'served_by');
    }

    public function getFullNameAttribute(){
        return $this->first_name . " " . $this->last_name;
    }

    public function getDoctorPendingQueues(){
        return $this->staffQueues()
                ->whereDate('created_at', Carbon::today())
                ->whereIn('status', ['SERVING', 'WAITING'])
                ->get();
    }

    public function getNursePendingQueues(){

        $queues = Queue::whereIn('status', ['WAITING', 'SERVING'])
                    ->where('specialty', 'Phamarcy')
                    ->whereDate('created_at', Carbon::today())
                    ->get();

        return $queues;
    }

    public function getCurrentServing(){
        return $this->staffQueues
        ->where('status', 'SERVING')
        ->load(['patient'])
        ->first();
    }

    public function getQueueHistory(){
        if($this->role == 'PATIENT'){
            $queues = $this->queues->whereIn('status', ['COMPLETED', 'CANCELLED'])->load(['feedback'])->makeHidden(['doctor', 'patient']);
        }else{
            $queues = $this->staffQueues->whereIn('status', ['COMPLETED', 'CANCELLED'])->load(['feedback'])->makeHidden(['doctor', 'patient']);;
        }
        return $queues;
    }

    public function getAppointmentHistory(){
        if($this->role == 'PATIENT'){
            $appointments = $this->appointments->whereIn('status', ['COMPLETED', 'CANCELLED'])->load(['feedback'])->makeHidden(['doctor', 'patient']);;
        }else{
            $appointments = $this->doctorAppointments->whereIn('status', ['COMPLETED', 'CANCELLED'])->load(['feedback'])->makeHidden(['doctor', 'patient']);;
        }
        return $appointments;
    }
}
