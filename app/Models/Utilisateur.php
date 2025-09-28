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
        'id_direction',
        'id_note',
        'entreprise'
    ];

    protected $hidden = [
        'password',
    ];

    public function compagnie()
    {
        return $this->belongsTo(Direction::class, 'id_direction');
    }

   public function modules()
{
    return $this->belongsTo(CentreOrdonnancement::class, 'id_centre');
} 

}
