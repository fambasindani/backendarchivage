<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = ['nom', 'email', 'password'];
    protected $hidden = ['password'];

    // ========== RELATIONS ==========

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function droits()
    {
        return $this->belongsToMany(Droit::class, 'user_droits');
    }

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'user_groupes');
    }

    // ===== CALCUL DES DROITS FINAUX =====
    public function droitsFinaux()
    {
        return $this->droits
            ->merge($this->roles->flatMap->droits)
            ->merge($this->groupes->flatMap->droits)
            ->unique('code');
    }
}
