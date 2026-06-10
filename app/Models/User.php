<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;

    protected $table = "usuarios";
    protected $connection = "timeit";

    protected $primaryKey = "id";

    public $timestamps = false;


    protected $appends = ['role', 'status', 'email', 'employee_id'];

    protected $hidden = [
        'clave',
        'correo',
        'rol',
        'estado',
        'Empleados_id'
    ];


    public function adminlte_image()
    {
        return $this->employee->curriculum->photo;
    }

    public function adminlte_desc()
    {
        return $this->employee->job->name;
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'Empleados_id');
    }

    public function scopeWithActiveEmployee($query)
    {
        return $query->whereHas('employee', function ($q) {
            $q->where('estado', 'Activo');
        });
    }

    public function getAuthPassword()
    {
        return $this->clave;
    }

    /* -----------------------------------------------------------------
     | Control de acceso por areas / funcionarios
     | ----------------------------------------------------------------- */

    public function isSuperadmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /** Area organizacional del funcionario: empleado -> cargo -> Areas_id. */
    public function areaId(): ?int
    {
        $areaId = $this->employee?->job?->area_id;
        return $areaId !== null ? (int) $areaId : null;
    }

    public function isBlocked(): bool
    {
        return BlockedUser::where('user_id', $this->id)->exists();
    }

    public function isSpecial(): bool
    {
        return SpecialUser::where('user_id', $this->id)->exists();
    }

    /** IDs de las areas de las que este usuario es responsable. */
    public function responsibleAreaIds(): array
    {
        return AreaResponsible::where('user_id', $this->id)->pluck('area_id')->all();
    }

    public function isResponsible(): bool
    {
        return AreaResponsible::where('user_id', $this->id)->exists();
    }

    public function isResponsibleFor(?int $areaId): bool
    {
        if ($areaId === null) {
            return false;
        }
        return AreaResponsible::where('user_id', $this->id)
            ->where('area_id', $areaId)
            ->exists();
    }

    /**
     * Compuerta de acceso a la plataforma. Cerrada por defecto.
     * superadmin siempre entra; un bloqueo individual niega (salvo superadmin);
     * los especiales entran; el resto solo si su area esta habilitada.
     */
    public function hasPlatformAccess(): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }
        if ($this->isBlocked()) {
            return false;
        }
        if ($this->isSpecial()) {
            return true;
        }
        $areaId = $this->areaId();
        return $areaId !== null && EnabledArea::where('area_id', $areaId)->exists();
    }

    public function getEmailAttribute()
    {
        return $this->attributes['correo'];
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['correo'] = $value;
    }

    public function getRoleAttribute()
    {
        return $this->attributes['rol'];
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['rol'] = $value;
    }

    public function getStatusAttribute()
    {
        return $this->attributes['estado'];
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['estado'] = $value;
    }

    public function getEmployeeIdAttribute()
    {
        return $this->attributes['Empleados_id'];
    }

    public function setEmployeeIdAttribute($value)
    {
        $this->attributes['Empleados_id'] = $value;
    }
}
