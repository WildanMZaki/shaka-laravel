<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presence extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'date', 'entry_at', 'photo', 'status', 'flag', 'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
