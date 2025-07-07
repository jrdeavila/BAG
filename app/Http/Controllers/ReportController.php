<?php

namespace App\Http\Controllers;

use App\Enums\ActivityPriority;
use App\Enums\ActivityStatus;
use App\Exports\ActivityExport;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view-activity-report');
    }
    public function __invoke(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|before:end_date',
            'end_date' => 'nullable|date|after:start_date',
            'start_time' => 'nullable|before:end_time',
            'status' => 'nullable|in:' . implode(',', array_map(fn($status) => $status->value, ActivityStatus::cases())),
            'priority' => 'nullable|in:' . implode(',', array_map(fn($priority) => $priority->value, ActivityPriority::cases())),
            'end_time' => 'nullable|after:start_time',
            'user_dni' => 'nullable|exists:' . Employee::class . ',noDocumento',
            'view' => 'nullable|in:list,tree,excel',
        ]);
        $userDNI = $request->get('user_dni');
        $user = User::whereHas('employee', function ($query) use ($userDNI) {
            $query->where('noDocumento', $userDNI);
        })->first();
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');
        $limit = $request->get('limit', 5);
        $status = $request->get('status');
        $view = $request->get('view');

        $priority = $request->get('priority');

        $activities = Activity::query()
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($priority, function ($query, $priority) {
                return $query->where('priority', $priority);
            })
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
            ->latest('date', 'end_time');
        if ($view === 'tree') {
            $activities = $activities->paginate(100);
        } else if ($view === 'excel') {
            $activities = $activities->get();
            return Excel::download(new ActivityExport($activities), 'activities.xlsx');
        } else {
            $activities = $activities
                ->paginate($limit);
        }


        return view('pages.reports.index', compact('activities'));
    }
}
