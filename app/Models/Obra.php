<?php

// app/Models/Obra.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obra extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'estado',
        'descripcion',
        'ubicacion',
        'fecha_inicio',
        'fecha_fin',
        'contratista_id',
        'inspector_id'
    ];

    protected $dates = ['fecha_inicio', 'fecha_fin'];

    /*public function usuarios()
    {
        return $this->belongsToMany(User::class, 'obra_usuario_rol', 'obra_id', 'user_id')
                    ->withPivot('rol_id')
                    ->withTimestamps();
    }*/

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'obra_user')
                    ->withPivot('rol_id')
                    ->withTimestamps();
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }
    public function ordenesServicio()
    {
        return $this->hasMany(OrdenServicio::class, 'obra_id');
    }

    public function libroObra()
    {
        return $this->hasMany(LibroObra::class)->orderBy('orden');
    }

    public function contratista()
    {
        return $this->belongsTo(User::class, 'contratista_id');
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
    public function entregasContratista()
    {
        return $this->hasMany(EntregaContratista::class, 'obra_id');
    }
}