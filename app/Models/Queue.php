<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('d-m-Y h:i A');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function served_by()
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    public function reason()
    {
        return $this->hasOne(Reason::class);
    }
}
