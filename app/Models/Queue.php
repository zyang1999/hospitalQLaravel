<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Queue extends Model
{
    use HasFactory;

    protected $appends = ['type', 'start_at'];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('d-m-Y');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'served_by');
    }
    

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function getTypeAttribute(){
        return 'Queue';
    }

    public function getStartAtAttribute(){
        return Carbon::parse($this->created_at)->format('h:i A');
    }
}
