<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nik',
        'is_employee',
        'access_id',
        'active',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function avatar()
    {
        return 'https://ui-avatars.com/api/?name=' . $this->name . '&color=343a55&background=f1f0f3';
    }

    public function photoPath()
    {
        return $this->photo ? asset("storage") . '/' . $this->photo : $this->avatar();
    }

    public function access()
    {
        return $this->belongsTo(Access::class);
    }

    public function leader()
    {
        return $this->belongsToMany(User::class, 'sales_teams', 'sales_id', 'leader_id')->limit(1);
    }

    public function sales()
    {
        return $this->belongsToMany(User::class, 'sales_teams', 'leader_id', 'sales_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    // Ini penjualan
    public function selling()
    {
        return $this->hasMany(Sale::class);
    }

    public function kasbons()
    {
        return $this->hasMany(Kasbon::class);
    }

    public function presenceThisWeek()
    {
        return Presence::hadBy($this->id);
    }
}
