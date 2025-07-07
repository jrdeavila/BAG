<?php

namespace Database\Seeders;

use App\Enums\ActivityPriority;
use App\Enums\ActivityStatus;
use App\Models\Activity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            [
                'description' => 'Realice aseo general en el primer piso',
                'date' => '2025-06-26',
                'start_time' => '09:00:00',
                'end_time' => '10:00:00',
                'observations' => 'Observaciones de la actividad 1',
                'user_id' => 639,
                'created_by' => 639,
            ],
            [
                'description' => 'Realice aseo general en el segundo piso',
                'date' => '2025-06-26',
                'start_time' => '10:00:00',
                'end_time' => '11:00:00',
                'observations' => 'Observaciones de la actividad 2',
                'user_id' => 639,
                'created_by' => 639,
            ],
            [
                'description' => 'Realice aseo general en el tercer piso',
                'date' => '2025-06-26',
                'start_time' => '11:00:00',
                'end_time' => '12:00:00',
                'observations' => 'Observaciones de la actividad 3',
                'user_id' => 639,
                'created_by' => 639,
            ],
            [
                'description' => 'Realice aseo general en el cuarto piso',
                'date' => '2025-06-26',
                'start_time' => '12:00:00',
                'end_time' => '13:00:00',
                'observations' => 'Observaciones de la actividad 4',
                'user_id' => 639,
                'created_by' => 639,
            ],
            [
                'description' => 'Realice aseo general en el quinto piso',
                'date' => '2025-06-26',
                'start_time' => '13:00:00',
                'end_time' => '14:00:00',
                'observations' => 'Observaciones de la actividad 5',
                'user_id' => 639,
                'created_by' => 639,
            ],
            [
                'description' => 'Realice aseo general en el sexto piso',
                'date' => '2025-06-26',
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
                'observations' => 'Observaciones de la actividad 6',
                'user_id' => 639,
                'created_by' => 639,
            ],
            [
                'description' => 'Realice aseo general en el septimo piso',
                'date' => '2025-06-26',
                'start_time' => '15:00:00',
                'end_time' => '16:00:00',
                'observations' => 'Observaciones de la actividad 7',
                'user_id' => 639,
                'created_by' => 639,
            ]

        ];

        foreach ($activities as $activity) {
            Activity::create([
                ...$activity,
                'status' => ActivityStatus::CREATED_BY_USER->value,
                'priority' => ActivityPriority::CREATED_BY_USER->value,
            ]);
        }
    }
}
