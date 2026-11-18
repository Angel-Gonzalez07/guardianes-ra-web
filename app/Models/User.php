<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
   use HasFactory, Notifiable;
    protected $fillable = ['name', 'email', 'password', 'role_id', 'activo'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function esAdmin(): bool
    {
    return $this->role?->nombre === 'admin';
    }

    public function esEmpleado(): bool
    {
    return $this->role?->nombre === 'empleado';
    }

    public function esCliente(): bool
    {
    return $this->role?->nombre === 'cliente';
    }

    // Admin o empleado: entran al panel interno
    public function esPersonal(): bool
    {
    return $this->esAdmin() || $this->esEmpleado();
    }

    public function etiquetaRol(): string
    {
    return match ($this->role?->nombre) {
        'admin'    => 'Administrador',
        'empleado' => 'Empleado',
        'cliente'  => 'Cliente',
        default    => '—',
     };
    
    }

}