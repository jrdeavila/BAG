<?php

namespace App\Http\Controllers;

use App\Enums\ActivityPriority;
use App\Enums\ActivityStatus;
use App\Exports\ActivityExport;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view-reports');
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
            'view' => 'nullable|in:list,tree,excel',
            'user_id' => 'nullable|exists:' . User::class . ',id',
        ]);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');
        $limit = $request->get('limit', 5);
        $status = $request->get('status');
        $view = $request->get('view');
        $userId = $request->get('user_id');

        $priority = $request->get('priority');

        $actor = Auth::user();
        // Responsable: reportes acotados a funcionarios de su(s) area(s). Superadmin: todo.
        $areaUserIds = $actor->isSuperadmin() ? null : $this->areaUserIds($actor->responsibleAreaIds());

        $activities = Activity::query()
            ->when($areaUserIds !== null, function ($query) use ($areaUserIds) {
                return $query->whereIn('user_id', $areaUserIds ?: [0]);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($priority, function ($query, $priority) {
                return $query->where('priority', $priority);
            })
            ->when($userId, function ($query, $userId) {
                return $query->where('user_id', $userId);
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

        $employeesQuery = User::withActiveEmployee();
        if (! $actor->isSuperadmin()) {
            $areaIds = $actor->responsibleAreaIds();
            $employeesQuery->whereHas('employee.job', fn($q) => $q->whereIn('Areas_id', $areaIds ?: [0]));
        }
        $employees = $employeesQuery->get();

        return view('pages.reports.index', compact('activities', 'employees'));
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
}
