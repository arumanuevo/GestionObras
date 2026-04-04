<?php

// app/Models/EntregaContratista.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaContratista extends Model
{
    use HasFactory;

    protected $table = 'entregas_contratista';

    protected $casts = [
        'obra_id' => 'integer',
        'creador_id' => 'integer',
        'recibida' => 'boolean',
        'fecha' => 'date',
        'plazo_recepcion' => 'integer',
    ];

    // app/Models/EntregaContratista.php
protected $fillable = [
    'obra_id',
    'numero',
    'asunto',
    'descripcion',
    'fecha',
    'creador_id',
    'tipo_entrega',
    'plazo_recepcion', // Asegurarse de que esté en la lista de fillable
    'prioridad',
    'estado',
    'recibida',
    'fecha_recepcion'
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
        return $this->belongsToMany(User::class, 'entrega_contratista_destinatarios', 'entrega_id', 'user_id')
                    ->withPivot('recibida', 'fecha_recepcion')
                    ->withTimestamps();
    }

    public function archivos()
    {
        return $this->hasMany(EntregaContratistaArchivo::class, 'entrega_id', 'id');
    }
}