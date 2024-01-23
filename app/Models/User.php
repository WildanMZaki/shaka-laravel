<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function access()
    {
        return $this->belongsTo(Access::class);
    }

    public function teamLeader()
    {
        return $this->hasOne(SalesTeam::class, 'leader_id');
    }

    // public function sales($id)
    // {
    //     $teams = SalesTeam::where('leader_id', $id)->get('sales_id');
    //     $salesIds = [];
    //     foreach ($teams as $sales) {
    //         $salesIds[] = $sales->sales_id;
    //     }
    //     $sales = User::whereIn('id', $salesIds)->get();
    //     return $sales;
    // }

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
}
