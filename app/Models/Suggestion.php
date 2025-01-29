<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    protected $table = 'suggestions';

    protected $fillable = [
        'legal_system',
        'answer',
        'situation_id',
        'user_id',
        'image',
    ];

    public function situation()
    {
        return $this->belongsTo(Situation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
