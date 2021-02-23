<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Queue extends Model
{
    use HasFactory;

    protected $appends = [
        "type",
        "start_at",
        "doctor_full_name",
        "patient_full_name",
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format("d-m-Y");
    }

    public function patient()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, "served_by");
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function getTypeAttribute()
    {
        return "Queue";
    }

    public function getStartAtAttribute()
    {
        return Carbon::parse($this->created_at)->format("h:i A");
    }

    public function getDoctorFullNameAttribute()
    {
        if ($this->doctor == null) {
            return null;
        }
        return $this->doctor->first_name . " " . $this->doctor->last_name;
    }

    public function getPatientFullNameAttribute()
    {
        $fullName = null;
        if ($this->patient != null) {
            $fullName =
                $this->patient->first_name . " " . $this->patient->last_name;
        }
        return $fullName;
    }
    public function getNumberofPatientsAttribute()
    {
        if ($this->specialty == "Pharmacist") {
            return Queue::where("specialty", $this->specialty)
                ->where("location", $this->location)
                ->where("status", "WAITING")
                ->where("created_at", ">", Carbon::today())
                ->where("created_at", "<", $this->created_at)
                ->count();
        }

        return $this->doctor
            ->staffQueues()
            ->where("status", "WAITING")
            ->where("created_at", ">", Carbon::today())
            ->where("created_at", "<", $this->created_at)
            ->count();
    }

    public function getTimeRangeAttribute()
    {
        $averageWaitingTime = $this->doctor->staffQueues
            ->where("status", "COMPLETED")
            ->avg("waiting_time");
        $numberOfPatients =
            $this->doctor
                ->staffQueues()
                ->where("created_at", ">", Carbon::today())
                ->where("created_at", "<", $this->created_at)
                ->where("status", "WAITING")
                ->count();
        $totalWaitingSeconds = $numberOfPatients * $averageWaitingTime;
        $currentTime = Carbon::now();
        $extimatedServedAt = $currentTime->addSeconds($totalWaitingSeconds);
        $timeRange =
            $extimatedServedAt->format("h:i A") .
            " - " .
            $extimatedServedAt->addMinutes(15)->format("h:i A");
        return $timeRange;
    }
}
