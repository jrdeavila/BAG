<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
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
            $user = User::whereHas('employee', function ($query) use ($userDNI) {
                $query->where('noDocumento', $userDNI);
            })->first();

            $user = User::find(Auth::id());
            if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
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

    public function create()
    {
        return view('pages.activities.create');
    }

    public function edit(Activity $activity)
    {
        return view('pages.activities.edit', compact('activity'));
    }

    public function show(Activity $activity)
    {
        return view('pages.activities.show', compact('activity'));
    }

    public function store(Request $request)
    {
        $request->validate([
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
                'user_id' => Auth::id()
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
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|before:end_time',
            'end_time' => 'required|after:start_time',
            'observations' => 'nullable|string|max:1000',
        ]);
        $data = $request->all();
        try {
            DB::beginTransaction();
            $activity->update($data);
            DB::commit();
            return redirect()->back()->with('success', 'Actividad actualizada correctamente');
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
        $serviceUrl = env('AUTHORIZATION_EMPLOYEE_DETAILS') . '?user_id=' . $user->id;
        return redirect()->away($serviceUrl);
    }
}
