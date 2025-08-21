<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Utilisateur extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'statut', // champ pour la suppression logique
    ];

    protected $hidden = [
        'password',
    ];

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class, 'id_compagnie');
    }

   public function modules()
{
    return $this->hasMany(Module::class, 'id_utilisateur');
}

}
