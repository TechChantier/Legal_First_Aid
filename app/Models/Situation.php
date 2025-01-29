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
        return $this->hasMany(Suggestion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
