<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Activity extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'start_time',
        'end_time',
        'duration',
        'quality',
        'memo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
