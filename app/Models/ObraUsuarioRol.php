<?php
// app/Models/ObraUsuarioRol.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraUsuarioRol extends Model
{
    use HasFactory;

    protected $table = 'obra_usuario_rol';

    protected $fillable = [
        'obra_id',
        'user_id',
        'rol_id'
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rol()
    {
        return $this->belongsTo(RoleObra::class, 'rol_id');
    }
    // app/Models/ObraUsuarioRol.php
    public function getRolAttribute()
    {
        if ($this->rol_id) {
            return RoleObra::find($this->rol_id);
        }
        return null;
    }

}
