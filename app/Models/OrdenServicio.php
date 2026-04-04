<?php

// app/Models/OrdenServicio.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenServicio extends Model
{
    use HasFactory;

    protected $table = 'ordenes_servicio';
    
    protected $fillable = [
        'obra_id',
        'nota_pedido_id',
        'Nro',
        'numero',
        'Tipo',
        'fecha',
        'fecha_vencimiento',
        'Tema',
        'texto',
        'Observaciones',
        'pdf_path',
        'creador_id',
        'destinatario_id',
        'Estado',
        'firmada',
        'cumplida_por',
        'fecha_cumplimiento',
        'leida', // Añadimos el campo leida
        'texto_pdf', // Añadido para almacenar el texto extraído del PDF
        'resumen_ai', // Añadido para almacenar el resumen generado por IA
    ];
    
    protected $casts = [
        'firmada' => 'boolean',
        'fecha' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'fecha_cumplimiento' => 'datetime',
        'leida' => 'boolean', // Añadimos el cast para leida
    ];

    protected $dates = ['fecha_emision', 'fecha_vencimiento', 'firma_fecha', 'fecha_respuesta'];

    

    protected $attributes = [
        'numero' => 0,  // Valor por defecto si el campo existe
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creador_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    public function firmadoPor()
    {
        return $this->belongsTo(User::class, 'firmado_por');
    }
    public function notaPedido()
    {
        return $this->belongsTo(Nota::class, 'nota_pedido_id');
    }

    public static function boot()
{
    parent::boot();

    static::saving(function ($model) {
        // Sincronizar Nro y numero si ambos existen
        if (isset($model->Nro) && isset($model->numero)) {
            $model->numero = $model->Nro;
        }
    });
}
}
