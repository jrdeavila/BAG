<?php

namespace App\Http\Controllers;

use App\Enums\ActivityPriority;
use App\Enums\ActivityStatus;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:list-activities')->only(['index']);
        $this->middleware('can:view-activity')->only(['show']);
        $this->middleware('can:create-activity')->only(['create', 'store']);
        $this->middleware('can:edit-activity')->only(['edit', 'update']);
        $this->middleware('can:delete-activity')->only(['destroy']);
        $this->middleware('can:show-activity-owner')->only(['showUserDetails']);
    }
    public function index(Request $request)
    {
        try {
            $request->validate([
                'user_dni' => 'nullable|exists:' . Employee::class . ',noDocumento',
                'date' => 'nullable|date',
                'start_time' => 'nullable|before:end_time',
                'end_time' => 'nullable|after:start_time',
            ]);
            $userDNI = $request->get('user_dni');
            $date = $request->get('date');
            $startTime = $request->get('start_time');
            $endTime = $request->get('end_time');

            $user = User::find(Auth::id());

            if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
                $user = User::whereHas('employee', function ($query) use ($userDNI) {
                    $query->where('noDocumento', $userDNI);
                })->first();
                $activities = Activity::query()
                    ->when($user, function ($query, $user) {
                        return $query->where('user_id', $user->id);
                    })
                    ->when($date, function ($query, $date) {
                        return $query->where('date', $date);
                    })
                    ->when($startTime, function ($query, $startTime) {
                        return $query->where('start_time', '>=', $startTime);
                    })
                    ->when($endTime, function ($query, $endTime) {
                        return $query->where('end_time', '<=', $endTime);
                    })
                    ->paginate(5);
            } else {
                $activities = Activity::query()
                    ->where('user_id', Auth::id())
                    ->when($date, function ($query, $date) {
                        return $query->where('date', $date);
                    })
                    ->when($startTime, function ($query, $startTime) {
                        return $query->where('start_time', $startTime);
                    })
                    ->when($endTime, function ($query, $endTime) {
                        return $query->where('end_time', $endTime);
                    })
                    ->paginate(5);
            }
            return view('pages.activities.index', compact('activities'));
        } catch (Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return redirect()->back()->withInput()->withErrors($e->validator);
            }
            return redirect()->back()->withInput()->with('error', 'Error al buscar la actividad');
        }
    }

    public function create(Request $request)
    {
        $employees = User::role('activity-user');
        $user = $employees->whereHas('employee', function ($query) use ($request) {
            $query->where('noDocumento', $request->get('user_dni'));
        })->first();
        if (request()->has('user_dni') && !$user) {
            return redirect()->back()->withInput()->withErrors('El empleado no existe o no tiene permiso para realizar actividades');
        }
        return view('pages.activities.create', compact('user', 'employees'));
    }

    public function edit(Activity $activity, Request $request)
    {
        $employees = User::role('activity-user');
        $removeUser = $request->get('remove_user');
        if ($removeUser) {
            $user = null;
        } else {
            $user = $activity->user;
        }
        if ($request->has('user_dni')) {
            $user = $employees->whereHas('employee', function ($query) use ($request, $activity) {
                $query->where('noDocumento', $request->get('user_dni'));
            })->first();
        }
        if (request()->has('user_dni') && !$user) {
            return redirect()->back()->withInput()->withErrors('El empleado no existe o no tiene permiso para realizar actividades');
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
            'priority' => 'nullable|in:' . implode(',', array_map(fn($priority) => $priority->value, ActivityPriority::cases())),
            'status' => 'nullable|in:' . implode(',', array_map(fn($status) => $status->value, ActivityStatus::cases())),
            'description' => 'required|string|min:5|max:255',
            'date' => 'required|date',
            'start_time' => 'required|before:end_time',
            'end_time' => 'required|after:start_time',
            'observations' => 'nullable|string|max:1000',
        ]);
        $data = $request->all();
        try {
            DB::beginTransaction();
            $activity = Activity::create([
                ...$data,
                'user_id' =>  $request->get('user_id', Auth::id()),
                'created_by' => Auth::id(),
                'status' => $request->get('status', ActivityStatus::CREATED_BY_USER),
                'priority' => $request->get('priority', ActivityPriority::CREATED_BY_USER),
            ]);
            DB::commit();
            return redirect()->route('activities.show', $activity->id)->with('success', 'Actividad creada correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al crear la actividad');
        }

        return redirect()->route('activities.index');
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
        try {
            DB::beginTransaction();
            $activity->user_id =  $request->get('user_id', $activity->user_id);
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
            return redirect()->back()->withInput()->with('error', 'Error al actualizar la actividad');
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
            return redirect()->route('activities.index')->with('error', 'Error al eliminar la actividad');
        }
    }

    public function showUserDetails(User $user)
    {
        $serviceUrl = env('AUTHORIZATION_EMPLOYEE_DETAILS') . '/' . $user->id;
        return redirect()->away($serviceUrl, [
            // con la cookie de

        ]);
    }

    public function finish(Activity $activity)
    {
        try {
            DB::beginTransaction();
            $endTime = $activity->end_time;
            $date = $activity->date;
            $endDate = Carbon::parse($date)->addHours($endTime)->format('Y-m-d H:i:s');
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
            return redirect()->route('activities.show', $activity->id)->with('error', 'Error al finalizar la actividad');
        }
    }
}
