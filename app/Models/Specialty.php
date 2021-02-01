<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialty',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function queues(){
        return $this->hasMany(Queue::class, 'served_by');
    }
}
