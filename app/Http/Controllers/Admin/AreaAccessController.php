<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\AreaResponsible;
use App\Models\BlockedUser;
use App\Models\EnabledArea;
use App\Models\SpecialUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Configuracion de acceso a la plataforma (solo superadmin, gate manage-areas).
 * Permite habilitar areas, designar responsables por area, marcar funcionarios
 * especiales y bloquear funcionarios individuales.
 */
class AreaAccessController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::orderBy('nombre')->get();
        $enabledAreaIds = EnabledArea::pluck('area_id')->all();
        $responsibles = AreaResponsible::with('user.employee')->get()->groupBy('area_id');
        $specials = SpecialUser::with('user.employee')->get();
        $blocked = BlockedUser::with('user.employee')->get();

        return view('pages.admin.areas.index', compact(
            'areas',
            'enabledAreaIds',
            'responsibles',
            'specials',
            'blocked'
        ));
    }

    public function toggleArea(Area $area)
    {
        $existing = EnabledArea::where('area_id', $area->id)->first();
        if ($existing) {
            $existing->delete();
            $message = "Area \"{$area->name}\" deshabilitada.";
        } else {
            EnabledArea::create(['area_id' => $area->id]);
            $message = "Area \"{$area->name}\" habilitada.";
        }

        return redirect()->route('admin.areas.index')->with('success', $message);
    }

    public function addResponsible(Request $request, Area $area)
    {
        $data = $request->validate([
            'user_dni' => 'required|string',
        ]);

        $user = $this->findActiveUserByDni($data['user_dni']);

        if (! $user) {
            return redirect()->route('admin.areas.index')
                ->with('warning', 'El funcionario no existe o no esta activo.');
        }

        AreaResponsible::firstOrCreate(['area_id' => $area->id, 'user_id' => $user->id]);

        return redirect()->route('admin.areas.index')
            ->with('success', "{$user->employee->full_name} es responsable de \"{$area->name}\".");
    }

    public function removeResponsible(Area $area, User $user)
    {
        AreaResponsible::where('area_id', $area->id)->where('user_id', $user->id)->delete();

        return redirect()->route('admin.areas.index')->with('success', 'Responsable retirado.');
    }

    public function addSpecial(Request $request)
    {
        $data = $request->validate([
            'user_dni' => 'required|string',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = $this->findActiveUserByDni($data['user_dni']);
        if (! $user) {
            return redirect()->route('admin.areas.index')
                ->with('warning', 'El funcionario no existe o no esta activo.');
        }

        SpecialUser::updateOrCreate(['user_id' => $user->id], ['reason' => $data['reason'] ?? null]);

        return redirect()->route('admin.areas.index')
            ->with('success', "{$user->employee->full_name} marcado como funcionario especial.");
    }

    public function removeSpecial(User $user)
    {
        SpecialUser::where('user_id', $user->id)->delete();

        return redirect()->route('admin.areas.index')->with('success', 'Funcionario especial retirado.');
    }

    public function addBlocked(Request $request)
    {
        $data = $request->validate([
            'user_dni' => 'required|string',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = $this->findActiveUserByDni($data['user_dni']);
        if (! $user) {
            return redirect()->route('admin.areas.index')
                ->with('warning', 'El funcionario no existe o no esta activo.');
        }

        if ($user->isSuperadmin()) {
            return redirect()->route('admin.areas.index')
                ->with('warning', 'No se puede bloquear a un superadmin.');
        }

        BlockedUser::updateOrCreate(['user_id' => $user->id], ['reason' => $data['reason'] ?? null]);

        return redirect()->route('admin.areas.index')
            ->with('success', "{$user->employee->full_name} fue bloqueado.");
    }

    public function removeBlocked(User $user)
    {
        BlockedUser::where('user_id', $user->id)->delete();

        return redirect()->route('admin.areas.index')->with('success', 'Bloqueo retirado.');
    }

    private function findActiveUserByDni(string $dni): ?User
    {
        return User::withActiveEmployee()
            ->whereHas('employee', fn($q) => $q->where('noDocumento', $dni))
            ->first();
    }
}
