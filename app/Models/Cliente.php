<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'user_id', 'nombre', 'apellidos', 'telefono', 'direccion',
        'ciudad', 'estado', 'codigo_postal', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
