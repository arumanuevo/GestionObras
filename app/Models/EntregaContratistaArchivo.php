<?php

// app/Models/EntregaContratistaArchivo.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaContratistaArchivo extends Model
{
    use HasFactory;

    protected $table = 'entrega_contratista_archivos';

    protected $casts = [
        'entrega_id' => 'integer',
        'tamano' => 'integer',
    ];

    protected $fillable = [
        'entrega_id',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'tipo',
        'tamano'
    ];

    public function entrega()
    {
        return $this->belongsTo(EntregaContratista::class, 'entrega_id', 'id');
    }
}