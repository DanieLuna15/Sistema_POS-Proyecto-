<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Caffeinated\Shinobi\Concerns\HasRolesAndPermissions;
class User extends Authenticatable
{
    use Notifiable, HasRolesAndPermissions;

    protected $fillable = [
        'name', 'email', 'password', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sales(){
        return $this->hasMany(Sale::class);
    }
    public function purchases(){
        return $this->hasMany(Purchase::class);
    }
    /*public function sendPasswordResetNotification($token){
        $this->notify(new ResetPasswordNotification($token));
    }*/
}
