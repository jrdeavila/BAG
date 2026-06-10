<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

/**
 * Autorizacion de actividades por modelo de capacidades:
 *  - superadmin: todo (concedido en Gate::before, no llega aqui).
 *  - responsable de area: gestiona actividades de su(s) area(s).
 *  - funcionario normal: crea / ve / edita observaciones / finaliza SUS actividades.
 */
class ActivityPolicy
{
    /** Area del empleado asignado a la actividad. */
    private function activityAreaId(Activity $activity): ?int
    {
        return $activity->user?->areaId();
    }

    private function ownsActivity(User $user, Activity $activity): bool
    {
        return (int) $activity->user_id === (int) $user->id;
    }

    private function managesActivityArea(User $user, Activity $activity): bool
    {
        return $user->isResponsibleFor($this->activityAreaId($activity));
    }

    public function viewAny(User $user): bool
    {
        // Cualquier usuario con acceso puede entrar al listado; el controlador
        // acota los datos (propias / por area / todas).
        return true;
    }

    public function view(User $user, Activity $activity): bool
    {
        return $this->ownsActivity($user, $activity) || $this->managesActivityArea($user, $activity);
    }

    public function create(User $user): bool
    {
        // Cualquier funcionario con acceso a la plataforma puede crear actividades
        // (para si mismo). Asignar a otros sigue restringido por la capacidad 'assign'.
        return true;
    }

    /** Asignar la actividad a otro empleado (incluye fijar estado/prioridad). */
    public function assign(User $user): bool
    {
        return $user->isResponsible();
    }

    public function update(User $user, Activity $activity): bool
    {
        return $this->ownsActivity($user, $activity) || $this->managesActivityArea($user, $activity);
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $this->managesActivityArea($user, $activity);
    }

    public function finish(User $user, Activity $activity): bool
    {
        return $this->ownsActivity($user, $activity) || $this->managesActivityArea($user, $activity);
    }

    /** Ver los datos del funcionario propietario de la actividad. */
    public function viewOwner(User $user, Activity $activity): bool
    {
        return $this->managesActivityArea($user, $activity);
    }
}
