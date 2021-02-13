<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}
