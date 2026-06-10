<?php

namespace App\Http\Controllers;

use App\Enums\ActivityPriority;
use App\Enums\ActivityStatus;
use App\Models\Activity;
use App\Models\EnabledArea;
use App\Models\SpecialUser;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
{
    public function __construct()
    {
        // index->viewAny, show->view, create/store->create, edit/update->update, destroy->delete
        $this->authorizeResource(Activity::class, 'activity');
    }

    public function index(Request $request)
    {
        try {
            $request->validate([
                'date' => 'nullable|date',
                'start_time' => 'nullable|before:end_time',
                'end_time' => 'nullable|after:start_time',
                'user_id' => 'nullable|exists:' . User::class . ',id',
            ]);

            $actor = Auth::user();
            $date = $request->get('date');
            $startTime = $request->get('start_time');
            $endTime = $request->get('end_time');
            $filterUserId = $request->get('user_id');

            $activities = Activity::query()
                ->when($actor->isSuperadmin(), function ($query) use ($filterUserId) {
                    // Superadmin ve todas; puede filtrar por funcionario.
                    return $query->when($filterUserId, fn($q, $id) => $q->where('user_id', $id));
                })
                ->when(! $actor->isSuperadmin() && $actor->isResponsible(), function ($query) use ($actor, $filterUserId) {
                    // Responsable: solo actividades de funcionarios de su(s) area(s).
                    $areaUserIds = $this->areaUserIds($actor->responsibleAreaIds());
                    return $query->whereIn('user_id', $areaUserIds ?: [0])
                        ->when($filterUserId, fn($q, $id) => $q->where('user_id', $id));
                })
                ->when(! $actor->isSuperadmin() && ! $actor->isResponsible(), function ($query) use ($actor) {
                    // Funcionario normal: solo las suyas.
                    return $query->where('user_id', $actor->id);
                })
                ->when($date, fn($q, $date) => $q->where('date', $date))
                ->when($startTime, fn($q, $startTime) => $q->where('start_time', '>=', $startTime))
                ->when($endTime, fn($q, $endTime) => $q->where('end_time', '<=', $endTime))
                ->paginate(5);

            $employees = $this->assignableEmployees($actor);
            $user = $filterUserId ? User::find($filterUserId) : null;

            return view('pages.activities.index', compact('activities', 'date', 'startTime', 'endTime', 'user', 'employees'));
        } catch (Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return redirect()->back()->withInput()->withErrors($e->validator);
            }
            return redirect()->back()->withInput()->with('warning', 'Error al buscar la actividad');
        }
    }

    public function create(Request $request)
    {
        // El selector de empleado usa busqueda dinamica (AJAX); aqui solo
        // reponemos la opcion previamente elegida si hubo error de validacion.
        $selectedEmployee = old('user_id')
            ? $this->assignableEmployeesQuery(Auth::user())->find(old('user_id'))
            : null;

        return view('pages.activities.create', compact('selectedEmployee'));
    }

    /**
     * Busqueda dinamica de funcionarios asignables (por nombre o cedula),
     * acotada al alcance del actor. Responde JSON para Select2.
     */
    public function searchEmployees(Request $request)
    {
        $this->authorize('assign', Activity::class);

        $term = trim((string) $request->get('q', ''));

        $employees = $this->assignableEmployeesQuery(Auth::user())
            ->when($term !== '', function ($query) use ($term) {
                $query->whereHas('employee', function ($q) use ($term) {
                    $q->where('noDocumento', 'like', "%{$term}%")
                        ->orWhere('nombres', 'like', "%{$term}%")
                        ->orWhere('apellidos', 'like', "%{$term}%")
                        ->orWhereRaw("CONCAT(nombres, ' ', apellidos) like ?", ["%{$term}%"]);
                });
            })
            ->limit(20)
            ->get();

        return response()->json(
            $employees->map(fn($u) => [
                'id' => $u->id,
                'text' => $u->employee->full_name . ' (' . $u->employee->noDocumento . ')',
            ])->values()
        );
    }

    public function edit(Activity $activity, Request $request)
    {
        $employees = $this->assignableEmployeesQuery(Auth::user());
        $removeUser = $request->get('remove_user');
        if ($removeUser) {
            $user = null;
        } else {
            $user = $activity->user;
        }
        if ($request->has('user_dni')) {
            $user = $employees->whereHas('employee', function ($query) use ($request) {
                $query->where('noDocumento', $request->get('user_dni'));
            })->first();
        }
        if (request()->has('user_dni') && !$user) {
            return redirect()->back()->withInput()->withErrors('El empleado no existe o no pertenece a tu area');
        }
        return view('pages.activities.edit', compact('activity', 'user', 'employees'));
    }

    public function show(Activity $activity)
    {
        return view('pages.activities.show', compact('activity'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:' . User::class . ',id',
            'description' => 'required|string',
            'priority' => 'nullable|in:' . implode(',', array_map(fn($priority) => $priority->value, ActivityPriority::cases())),
            'status' => 'nullable|in:' . implode(',', array_map(fn($status) => $status->value, ActivityStatus::cases())),
            'date' => 'required|date',
            'start_time' => 'required|before:end_time',
            'end_time' => 'required|after:start_time',
            'observations' => 'nullable|string|max:1000',
        ]);

        $targetUserId = $this->resolveAssignedUserId($request);

        try {
            DB::beginTransaction();
            $data = $request->all();
            $activity = Activity::create([
                ...$data,
                'user_id' => $targetUserId,
                'created_by' => Auth::id(),
                'status' => $request->get('status', ActivityStatus::CREATED_BY_USER->value),
                'priority' => $request->get('priority', ActivityPriority::CREATED_BY_USER->value),
            ]);
            DB::commit();
            return redirect()->route('activities.show', $activity->id)->with('success', 'Actividad creada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->withInput()->with('warning', 'Error al crear la actividad');
        }
    }

    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'user_id' => 'nullable|exists:' . User::class . ',id',
            'priority' => 'nullable|in:' . implode(',', array_map(fn($priority) => $priority->value, ActivityPriority::cases())),
            'status' => 'nullable|in:' . implode(',', array_map(fn($status) => $status->value, ActivityStatus::cases())),
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|before:end_time',
            'end_time' => 'required|after:start_time',
            'observations' => 'nullable|string|max:1000',
        ]);

        $targetUserId = $request->has('user_id')
            ? $this->resolveAssignedUserId($request, $activity->user_id)
            : $activity->user_id;

        try {
            DB::beginTransaction();
            $activity->user_id = $targetUserId;
            $activity->status = $request->get('status', $activity->status);
            $activity->priority = $request->get('priority', $activity->priority);
            $activity->description = $request->get('description', $activity->description);
            $activity->date = $request->get('date', $activity->date);
            $activity->start_time = $request->get('start_time', $activity->start_time);
            $activity->end_time = $request->get('end_time', $activity->end_time);
            $activity->observations = $request->get('observations', $activity->observations);
            $activity->save();
            DB::commit();
            return redirect()->route('activities.show', $activity->id)->with('success', 'Actividad actualizada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->withInput()->with('warning', 'Error al actualizar la actividad');
        }
    }

    public function destroy(Activity $activity)
    {
        try {
            DB::beginTransaction();
            $activity->delete();
            DB::commit();
            return redirect()->route('activities.index')->with('success', 'Actividad eliminada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->route('activities.index')->with('warning', 'Error al eliminar la actividad');
        }
    }

    public function showUserDetails(User $user)
    {
        $actor = Auth::user();
        abort_unless($actor->isSuperadmin() || $actor->isResponsibleFor($user->areaId()), 403);

        $serviceUrl = env('AUTHORIZATION_EMPLOYEE_DETAILS') . '/' . $user->id;
        return redirect()->away($serviceUrl);
    }

    public function finish(Activity $activity)
    {
        $this->authorize('finish', $activity);
        try {
            DB::beginTransaction();
            $endTime = $activity->end_time;
            [$hour, $minute] = explode(':', $endTime);
            $date = $activity->date;
            $endDate = Carbon::parse($date)->setTime($hour, $minute);
            $isLate = Carbon::now()->gt($endDate);
            if ($isLate) {
                $activity->status = ActivityStatus::FINISHED_LATE;
            } else {
                $activity->status = ActivityStatus::FINISHED;
            }
            $activity->save();
            DB::commit();
            return redirect()->route('activities.show', $activity->id)->with('success', 'Actividad finalizada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->route('activities.show', $activity->id)->with('warning', 'Error al finalizar la actividad');
        }
    }

    /* -----------------------------------------------------------------
     | Helpers de alcance por area
     | ----------------------------------------------------------------- */

    /**
     * Query de funcionarios activos que el actor puede asignar.
     * - superadmin: funcionarios de todas las areas habilitadas + funcionarios especiales.
     * - responsable: funcionarios de su(s) area(s).
     */
    private function assignableEmployeesQuery(User $actor)
    {
        $query = User::withActiveEmployee();

        if ($actor->isSuperadmin()) {
            $enabledAreaIds = EnabledArea::pluck('area_id')->all();
            $specialUserIds = SpecialUser::pluck('user_id')->all();
            $query->where(function ($q) use ($enabledAreaIds, $specialUserIds) {
                $q->whereHas('employee.job', fn($j) => $j->whereIn('Areas_id', $enabledAreaIds ?: [0]))
                    ->orWhereIn('id', $specialUserIds ?: [0]);
            });
        } else {
            $areaIds = $actor->responsibleAreaIds();
            $query->whereHas('employee.job', fn($q) => $q->whereIn('Areas_id', $areaIds ?: [0]));
        }

        return $query;
    }

    private function assignableEmployees(User $actor)
    {
        return $this->assignableEmployeesQuery($actor)->get();
    }

    /** IDs de usuarios (funcionarios activos) pertenecientes a las areas dadas. */
    private function areaUserIds(array $areaIds): array
    {
        if (empty($areaIds)) {
            return [];
        }
        return User::withActiveEmployee()
            ->whereHas('employee.job', fn($q) => $q->whereIn('Areas_id', $areaIds))
            ->pluck('id')
            ->all();
    }

    /**
     * Resuelve a quien se asigna la actividad validando el alcance del actor.
     * Si no se especifica otro funcionario, se asigna al propio actor.
     */
    private function resolveAssignedUserId(Request $request, ?int $fallback = null): int
    {
        $actor = Auth::user();
        $requested = $request->get('user_id');

        if ($requested === null || (int) $requested === (int) $actor->id) {
            return $fallback ?? (int) $actor->id;
        }

        // Asignar a otro: requiere capacidad de asignacion y que el destino este en su alcance.
        $this->authorize('assign', Activity::class);
        if (! $actor->isSuperadmin()) {
            $targetAreaId = User::find($requested)?->areaId();
            abort_unless($actor->isResponsibleFor($targetAreaId), 403);
        }
        return (int) $requested;
    }
}
