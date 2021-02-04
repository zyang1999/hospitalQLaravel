<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $appends = ['type', 'date_string', 'doctor_full_name', 'patient_full_name'];

    protected $fillable = ['date', 'start_at', 'specialty', 'status', 'location', 'end_at'];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function feedback()
    {
        return $this->hasOne(AppointmentFeedback::class);
    }
    
    public function getTypeAttribute()
    {
        return 'Appointment';
    }

    public function getDateStringAttribute(){
        return Carbon::parse($this->date)->format('d-m-Y');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    public function getDoctorFullNameAttribute(){
        return $this->doctor->first_name .' '.$this->doctor->last_name;
    }

    public function getPatientFullNameAttribute(){
        $fullName = null;
        if($this->patient != null){
            $fullName = $this->patient->first_name.' '.$this->patient->last_name;
        }
        return $fullName;
    }

    protected $casts = [
        'start_at' => 'datetime:h:i A',
        'end_at' => 'datetime:h:i A',
        'date' => 'datetime:d-m-Y'
    ];
}
