<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Situation extends Model
{
    protected $table = 'situations';

    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];

    public function suggestions()
    {
        $this->hasMany(Suggestion::class);
    }

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
