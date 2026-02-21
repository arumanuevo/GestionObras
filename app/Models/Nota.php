<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'texto_pdf'
    ];
    

    protected $dates = ['fecha'];
}


