<?php

// app/Models/RoleObra.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleObra extends Model
{
    use HasFactory;

    protected $table = 'roles_obra';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function usuarios()
    {
        return $this->hasMany(ObraUsuarioRol::class, 'rol_id');
    }
}
