<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentDeclaration extends Model
{
    use HasFactory;
     protected $fillable = ['id_declaration',   'id_classeur', 'nom_fichier', 'nom_native'];


}
