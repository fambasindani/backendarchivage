<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentreOrdonnancement extends Model
{
    use HasFactory;


    protected $fillable = [
        
        'nom', // Nouveau champ
        'description',
        'statut',
        
    ];
}
