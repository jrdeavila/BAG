<?php

namespace App\Exports;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivityExport implements FromCollection, WithMapping, WithHeadings
{

    private Collection $activities;
    public function __construct(Collection $activities)
    {
        $this->activities = $activities;
    }

    public function collection()
    {
        return $this->activities;
    }

    public function headings(): array
    {
        return array_map(fn($name) => trans('messages.activities.' . $name . '_label'), [
            'id',
            'description',
            'observations',
            'status',
            'priority',
            "user_name",
            "user_dni",
            "date",
            "start_time",
            "end_time",
            'created_by_name',
            'created_by_dni',
            'created_at',
            'updated_at'
        ]);
    }

    public function map($activity): array
    {
        return [
            $activity->id,
            $activity->description,
            $activity->observations,
            trans("messages." . $activity->status),
            trans("messages." . $activity->priority),
            $activity->user->employee->full_name,
            $activity->user->employee->document_number,
            $activity->date,
            $activity->start_time,
            $activity->end_time,
            $activity->createdBy->employee->full_name,
            $activity->createdBy->employee->document_number,
            $activity->created_at,
            $activity->updated_at,
        ];
    }
}
