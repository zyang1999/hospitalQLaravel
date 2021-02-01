<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'start_at', 'specialty', 'status', 'location', 'end_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function reason()
    {
        return $this->hasOne(AppointmentReason::class);
    }
    public function feedback()
    {
        return $this->hasOne(AppointmentFeedback::class);
    }

    protected $casts = [
        'start_at' => 'datetime:h:i A',
        'end_at' => 'datetime:h:i A',
        'date' => 'datetime:d-m-Y'
    ];

}
