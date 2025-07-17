<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Actualización de Actividad</title>
</head>

<body style="font-family: Arial, sans-serif; color: #222;">
    @php
        $status = $activity->status;
    @endphp

    @if ($status === \App\Enums\ActivityStatus::FINISHED->value)
        <h2 style="color: #28a745;">✅ ¡Actividad Completada!</h2>
        <p>La actividad "{{ $activity->description }}" ha sido marcada como <strong>completada</strong>.</p>
    @elseif($status === \App\Enums\ActivityStatus::PENDING->value)
        <h2 style="color: #ffc107;">🕒 Actividad Pendiente</h2>
        <p>La actividad "{{ $activity->description }}" sigue en estado <strong>pendiente</strong>.</p>
    @elseif($status === \App\Enums\ActivityStatus::CANCELLED->value)
        <h2 style="color: #dc3545;">❌ Actividad Cancelada</h2>
        <p>La actividad "{{ $activity->description }}" ha sido <strong>cancelada</strong>.</p>
    @elseif($status === \App\Enums\ActivityStatus::PAUSED->value)
        <h2 style="color: #17a2b8;">⏸️ Actividad en Pausa</h2>
        <p>La actividad "{{ $activity->description }}" está actualmente <strong>en pausa</strong>.</p>
    @elseif($status === \App\Enums\ActivityStatus::FINISHED_LATE->value)
        <h2 style="color: #fd7e14;">✅⏰ Actividad Finalizada Fuera de Tiempo</h2>
        <p>La actividad "{{ $activity->description }}" fue <strong>finalizada fuera del tiempo programado</strong>.</p>
    @elseif($status === \App\Enums\ActivityStatus::LATE->value)
        <h2 style="color: #dc3545;">⏰ Actividad Retrasada</h2>
        <p>La actividad "{{ $activity->description }}" está <strong>retrasada</strong>.</p>
    @elseif($status === \App\Enums\ActivityStatus::CREATED_BY_USER->value)
        <h2 style="color: #007bff;">📝 Actividad Registrada por Usuario</h2>
        <p>La actividad "{{ $activity->description }}" fue <strong>registrada directamente por el usuario</strong>.</p>
    @else
        <h2 style="color: #6c757d;">🔄 Actualización de Actividad</h2>
        <p>La actividad "{{ $activity->description }}" ha cambiado de estado a
            <strong>{{ __('messages.' . $status->value) }}</strong>.
        </p>
    @endif

    <hr>

    <h3>📝 Detalles de la Actividad</h3>
    <ul>
        <li><strong>Asignada a:</strong> {{ $activity->user->employee->full_name }}</li>
        <li><strong>Fecha:</strong> {{ $activity->date->format('d/m/Y') }}</li>
        <li><strong>Prioridad:</strong> {{ __('messages.' . $activity->priority) }}</li>
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
            style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 4px;">
            Ver Actividad
        </a>
    </p>

    <p>¡Gracias por usar {{ env('APP_NAME') }}!</p>
</body>

</html>
