<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date', 'entry_at', 'photo', 'status', 'flag', 'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
