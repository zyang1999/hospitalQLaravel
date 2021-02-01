<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        'role'
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
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['selfie_string'];

    public function getSelfieStringAttribute()
    {
        $data = file_get_contents($this->selfie);
        $base64 = 'data:image/png;base64,' . base64_encode($data);
        return $base64;
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function office()
    {
        return $this->hasOne(Office::class);
    }

    public function specialties(){
        return $this->hasMany(Specialty::class, 'doctor_id');
    }

    public function appointments(){
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function doctorAppointments(){
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function doctorQueues(){
        return $this->hasMany(Queue::class, 'served_by');
    }
}
