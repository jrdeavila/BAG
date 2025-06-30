<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:show-activity-report');
    }
    public function __invoke(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|before:end_date',
            'end_date' => 'nullable|date|after:start_date',
            'start_time' => 'nullable|before:end_time',
            'end_time' => 'nullable|after:start_time',
            'user_dni' => 'nullable|exists:' . Employee::class . ',noDocumento',
        ]);
        $userDNI = $request->get('user_dni');
        $user = User::whereHas('employee', function ($query) use ($userDNI) {
            $query->where('noDocumento', $userDNI);
        })->first();
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');

        $activities = Activity::query()
            ->when($user, function ($query, $user) {
                return $query->where('user_id', $user->id);
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->where('date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where('date', '<=', $endDate);
            })
            ->when($startTime, function ($query, $startTime) {
                return $query->where('start_time', '>=', $startTime);
            })
            ->when($endTime, function ($query, $endTime) {
                return $query->where('end_time', '<=', $endTime);
            })
            ->get();


        return view('pages.reports.index', compact('activities'));
    }
}
