<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Te han asignado una nueva actividad</title>
</head>

<body style="font-family: Arial, sans-serif; color: #222;">
    <h2 style="color: #28a745;">🆕 ¡Te han asignado una nueva actividad!</h2>

    <p>Hola {{ $activity->user->employee->full_name }},</p>

    <p>El gestor {{ $activity->createdBy->employee->full_name }} ha registrado una nueva actividad para ti en
        {{ env('APP_NAME') }}.
    </p>

    <hr>

    <h3>📝 Detalles de la Actividad</h3>
    <ul>
        <li><strong>Descripción:</strong> {{ $activity->description }}</li>
        <li><strong>Fecha:</strong> {{ $activity->date->format('d/m/Y') }}</li>
        <li><strong>Prioridad:</strong> {{ __('messages.' . $activity->priority) }}</li>
        <li><strong>Estado:</strong> {{ __('messages.' . $activity->status) }}</li>
        @if ($activity->start_time)
            <li><strong>Hora de inicio:</strong> {{ $activity->start_time }}</li>
        @endif
        @if ($activity->end_time)
            <li><strong>Hora de fin:</strong> {{ $activity->end_time }}</li>
        @endif
        @if ($activity->observations)
            <li><strong>Observaciones:</strong> {{ $activity->observations }}</li>
        @endif
    </ul>

    <p>
        <a href="{{ route('activities.show', $activity->id) }}"
            style="display: inline-block; padding: 10px 20px; background-color: #28a745; color: #fff; text-decoration: none; border-radius: 4px;">
            Ver Actividad
        </a>
    </p>

    <p>¡Te deseamos una excelente gestión!</p>
</body>

</html>
