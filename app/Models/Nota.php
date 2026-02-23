<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Nota extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Añadimos el campo user_id
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
        'destinatario_id'
    ];

    protected $dates = ['fecha', 'created_at', 'updated_at'];

    // Mutador para la fecha
    public function setFechaAttribute($value)
    {
        $this->attributes['fecha'] = $value ? Carbon::createFromFormat('Y-m-d', $value) : null;
    }

    // Accesor para la fecha
    public function getFechaAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    // Relación con el usuario destinatario
    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    // Relación con el creador
    public function creador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}





