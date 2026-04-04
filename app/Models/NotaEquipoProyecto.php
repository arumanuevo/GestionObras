<?php

// app/Models/NotaEquipoProyecto.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaEquipoProyecto extends Model
{
    use HasFactory;

    protected $table = 'notas_equipo_proyecto';

    protected $casts = [
        'obra_id' => 'integer',
        'creador_id' => 'integer',
        'leida' => 'boolean',
        'fecha' => 'date',
        'plazo_entrega' => 'integer',
    ];

    protected $fillable = [
        'obra_id',
        'numero',
        'tema',
        'contenido',
        'fecha',
        'creador_id',
        'leida',
        'tipo_entrega',
        'plazo_entrega',
        'prioridad',
        'estado'
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class, 'obra_id', 'id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creador_id', 'id');
    }

    public function destinatarios()
    {
        return $this->belongsToMany(User::class, 'nota_equipo_destinatarios', 'nota_equipo_id', 'user_id')
                    ->withPivot('leida')
                    ->withTimestamps();
    }

    public function archivos()
    {
        return $this->hasMany(NotaEquipoArchivo::class, 'nota_equipo_id', 'id');
    }
}