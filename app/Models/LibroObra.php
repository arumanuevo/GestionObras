<?php

// app/Models/LibroObra.php
// app/Models/LibroObra.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibroObra extends Model
{
    use HasFactory;

    protected $table = 'libro_obras';

    protected $fillable = [
        'obra_id',
        'documento_type',
        'documento_id',
        'orden',
        'fecha_registro'
    ];

    protected $dates = ['fecha_registro'];

    public function obra()
    {
        return $this->belongsTo(Obra::class, 'obra_id');
    }

    public function documento()
    {
        return $this->morphTo();
    }
}
