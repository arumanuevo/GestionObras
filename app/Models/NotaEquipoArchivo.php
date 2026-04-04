<?php

// app/Models/NotaEquipoArchivo.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaEquipoArchivo extends Model
{
    use HasFactory;

    protected $table = 'nota_equipo_archivos';

    protected $casts = [
        'nota_equipo_id' => 'integer',
        'tamano' => 'integer',
    ];

    protected $fillable = [
        'nota_equipo_id',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'tipo',
        'tamano'
    ];

    public function nota()
    {
        return $this->belongsTo(NotaEquipoProyecto::class, 'nota_equipo_id', 'id');
    }
}