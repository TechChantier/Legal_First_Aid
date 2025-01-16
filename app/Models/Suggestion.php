<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    protected $table = 'situations';

    protected $fillable = [
        'answer',
        'situation_id',
        'user_id',
        'image'
    ];

    public function situation()
    {
        $this->belongsTo(Situation::class);
    }

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
