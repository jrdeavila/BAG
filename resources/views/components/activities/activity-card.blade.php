@props(['activity'])

<x-adminlte-card onclick="window.location='{{ route('activities.show', $activity->id) }}';"
    title="{{ \Carbon\Carbon::parse($activity->date)->format('d-m-Y') }}" theme="info" icon="fas fa-clipboard-list">
    <dl class="row">
        <dt class="col-sm-4">Descripción:</dt>
        <dd class="col-sm-8">{{ $activity->description }}</dd>

        <dt class="col-sm-4">Hora de inicio:</dt>
        <dd class="col-sm-8">{{ $activity->start_time }}</dd>

        <dt class="col-sm-4">Hora de finalización:</dt>
        <dd class="col-sm-8">{{ $activity->end_time }}</dd>

    </dl>
</x-adminlte-card>
