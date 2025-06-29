<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'user_dni' => 'nullable|exists:users,dni',
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
            $activities = Activity::query()
                ->when($userDNI, function ($query, $userDNI) {
                    return $query->where('user_dni', $userDNI);
                })
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
        Activity::create([
            ...$data,
            'user_id' => Auth::id()
        ]);
        return redirect()->route('activities.index');
    }

    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|time|before:end_time',
            'end_time' => 'required|time|after:start_time',
            'observations' => 'nullable|string|max:1000',
        ]);
        $data = $request->all();
        $activity->update($data);
        return redirect()->route('activities.index');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('activities.index');
    }

    public function showUserDetails(User $user)
    {
        $serviceUrl = env('AUTHORIZATION_EMPLOYEE_DETAILS') . '?user_id=' . $user->id;
        return redirect()->away($serviceUrl);
    }
}
