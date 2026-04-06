<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\CustomResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'organization',
        'position',
        'profile_photo_path',
        'profile_completed',
        'approved',
        'approved_by',
        'approved_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved' => 'boolean',
        'approved_at' => 'datetime',
        'profile_completed' => 'boolean',
    ];

    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        // Usamos una imagen de perfil genérica
        return 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';
    }

    public function sendEmailVerificationNotification()
    {
        if (!$this->hasVerifiedEmail()) {
            $this->notify(new VerifyEmailNotification());
        }
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

   /* public function obras()
    {
        return $this->belongsToMany(Obra::class, 'obra_usuario_rol', 'user_id', 'obra_id')
                    ->withPivot('rol_id')
                    ->withTimestamps();
    }*/
    public function obras()
{
    return $this->belongsToMany(Obra::class, 'obra_user')
                ->withPivot('rol_id')
                ->withTimestamps();
}

    public function rolesEnObra(Obra $obra)
    {
        return $this->obras()->where('obra_id', $obra->id)->withPivot('rol_id')->first()->pivot->rol_id;
    }
    public function entregasContratistaDestinatario()
{
    return $this->belongsToMany(EntregaContratista::class, 'entrega_contratista_destinatarios', 'user_id', 'entrega_id')
                ->withPivot('recibida', 'fecha_recepcion')
                ->withTimestamps();
}

public function getRolEnObra($obraId)
{
    $obraUsuarioRol = $this->obraUsuarioRol()->where('obra_id', $obraId)->first();
    return $obraUsuarioRol ? $obraUsuarioRol->rol : null;
}
public function obraUsuarioRol()
{
    return $this->hasMany(ObraUsuarioRol::class, 'user_id');
}
}
