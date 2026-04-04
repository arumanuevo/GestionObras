<?php

// app/Models/Nota.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Nota extends Model
{
    use HasFactory;

    protected $fillable = [
        'obra_id',
        'user_id',
        'Tipo',
        'Nro',
        'Tema',
        'texto',
        'fecha',
        'Rta_a_NP',
        'Respondida_por',
        'Observaciones',
        'Estado',
        'link',
        'pdf_path',
        'resumen_ai',
        'texto_pdf',
        'destinatario_id',
        'firmado_por',
        'firma_fecha',
        'leida',
        'fecha_lectura'
    ];

    protected $dates = ['fecha', 'firma_fecha', 'created_at', 'updated_at'];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_lectura' => 'datetime',
    ];
    
    // Mutadores para las fechas
    public function setFechaAttribute($value)
    {
        $this->attributes['fecha'] = $value ? Carbon::createFromFormat('Y-m-d', $value) : null;
    }

    public function setFirmaFechaAttribute($value)
    {
        $this->attributes['firma_fecha'] = $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value) : null;
    }

    // Accesores para las fechas
    public function getFechaAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getFirmaFechaAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    public function libroObra()
    {
        return $this->morphOne(LibroObra::class, 'documento');
    }

    public function ordenServicio()
    {
        return $this->hasOne(OrdenServicio::class, 'nota_pedido_id');
    }
}
