<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use HasPermissions;

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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'Empleados_id');
    }

    public function getAuthPassword()
    {
        return $this->clave;
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
