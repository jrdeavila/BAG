@extends('layouts.app')

@section('title', 'Reportes')

@section('header')
    <h1>Reportes</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        @can('assign-activity')
            <div class="d-none d-lg-flex col-lg-10 justify-content-start align-items-center mb-3">
                <x-adminlte-button theme="info" class="btn-flat" type="button" label="Asignar actividad"
                    icon="fas fa-clipboard-list" onclick="window.location='{{ route('activities.create') }}';" />
            </div>
            <div class="d-lg-none position-fixed d-block" style="bottom: 20px; right: 20px; z-index: 1000;">
                <x-adminlte-button theme="info" class="btn-flat" type="button" label="Asignar actividad"
                    icon="fas fa-clipboard-list" onclick="window.location='{{ route('activities.create') }}';" />
            </div>
        @endcan

        <div class="col-lg-10">
            <x-adminlte-card title="Filtros" theme="info" icon="fas fa-filter">
                <form class="row" action="{{ route('reports.index') }}" method="GET">
                    @csrf
                    <input type="hidden" name="view" value="{{ request('view', 'list') }}">
                    <div class="col-lg-5 col-md-4">
                        <x-adminlte-input value="{{ old('user_dni', request('user_dni')) }}" name="user_dni"
                            label="Documento del empleado" type="number" fgroup-class="col-md-12" />
                    </div>
                    <x-adminlte-select name="status" label="Estado" fgroup-class="col-lg-3 col-md-4">
                        <option value="">Todos</option>
                        @foreach (\App\Enums\ActivityStatus::cases() as $key => $status)
                            <option value="{{ $status->value }}"
                                {{ old('status', request('status')) == $status->value ? 'selected' : '' }}>
                                {{ __('messages.' . $status->name) }}</option>
                        @endforeach
                    </x-adminlte-select>
                    <x-adminlte-select name="priority" label="Prioridad" fgroup-class="col-lg-4 col-md-4">
                        <option value="">Todos</option>
                        @foreach (\App\Enums\ActivityPriority::cases() as $key => $priority)
                            <option value="{{ $priority->value }}"
                                {{ old('priority', request('priority')) == $priority->value ? 'selected' : '' }}>
                                {{ __('messages.' . $priority->name) }}</option>
                        @endforeach
                    </x-adminlte-select>
                    <x-adminlte-input value="{{ old('start_date', request('start_date')) }}" name="start_date"
                        label="Fecha de inicio" type="date" fgroup-class="col-lg-3 col-md-4" />
                    <x-adminlte-input value="{{ old('end_date', request('end_date')) }}" name="end_date"
                        label="Fecha de finalización" type="date" fgroup-class="col-lg-3 col-md-4" />
                    <x-adminlte-input value="{{ old('start_time') }}" name="start_time" label="Hora de inicio"
                        type="time" fgroup-class="col-lg-3 col-md-4" />
                    <x-adminlte-input value="{{ old('end_time') }}" name="end_time" label="Hora de finalización"
                        type="time" fgroup-class="col-lg-3 col-md-4" />
                    <x-adminlte-select name="limit" label="Mostrar" fgroup-class="col-lg-3 col-md-4">
                        <option value="5" @if (request('limit') == 5) selected @endif>5</option>
                        <option value="10" @if (request('limit') == 10) selected @endif>10</option>
                        <option value="25" @if (request('limit') == 25) selected @endif>25</option>
                        <option value="50" @if (request('limit') == 50) selected @endif>50</option>
                        <option value="100" @if (request('limit') == 100) selected @endif>100</option>
                    </x-adminlte-select>
                    <div class="col-lg-3 col-md-4 d-flex align-items-end pb-3">
                        <x-adminlte-button theme="info" class="btn-flat w-100" type="submit" label="Filtrar"
                            icon="fas fa-filter" />
                    </div>

                    @if (count(request()->all()) > 0)
                        <div class="col-lg-3 col-md-4 d-flex align-items-end pb-3">
                            <x-adminlte-button theme="info" class="btn-flat w-100" type="button" label="Limpiar"
                                icon="fas fa-eraser" onclick="window.location='{{ route('reports.index') }}';" />
                        </div>
                    @endif
                </form>
            </x-adminlte-card>
        </div>
        <div class="col-lg-10 mb-3">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center">
                    <span class="text-muted text-sm">
                        Mostrando {{ $activities->count() }} de {{ $activities->total() }} actividades encontradas
                    </span>
                </div>

                <div class="col-md-6 d-flex flex-row-reverse">
                    {{ $activities->appends(request()->all())->links('custom.pagination') }}
                </div>
                <div class="col-md-12 d-flex justify-content-center align-items-center mt-3">
                    <x-adminlte-button :theme="request('view') === 'list' || request('view') === null ? 'primary' : 'secondary'"
                        onclick="window.location='{{ route('reports.index', ['view' => 'list'] + request()->all()) }}';"
                        tooltip="Vista de lista" icon="fas fa-list" class="btn-flat" type="button" />
                    <x-adminlte-button :theme="request('view') === 'tree' ? 'primary' : 'secondary'" tooltip="Linea de tiempo"
                        onclick="window.location='{{ route('reports.index', ['view' => 'tree'] + request()->all()) }}';"
                        icon="fas fa-code-branch" class="btn-flat" type="button" />
                    <x-adminlte-button theme="success" class="btn-flat" type="button" icon="fas fa-file-excel"
                        tooltip="Exportar a Excel"
                        onclick="window.location='{{ route('reports.index', ['view' => 'excel'] + request()->all()) }}';" />

                </div>
            </div>
        </div>
        @if (request('view') === 'list' || request('view') === null)

            <div class="col-lg-10">
                <div class="row">
                    @foreach ($activities as $activity)
                        <div class="col-lg-4 col-md-6">
                            <x-activities.activity-card :activity="$activity" :with-user-details="true" :redirect="true" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @if (request('view') === 'tree')

            <div class="col-lg-10">
                <x-timeline>
                    @php
                        $groupedActivities = $activities->groupBy(function ($activity) {
                            return $activity->created_at->format('Y-m-d');
                        });
                    @endphp

                    @foreach ($groupedActivities as $date => $activities)
                        <x-timeline-label text="{{ $date }}"
                            tooltip="Actividades del día {{ $date }}" />
                        @foreach ($activities as $activity)
                            @php
                                $icon = match (\App\Enums\ActivityStatus::from($activity->status)) {
                                    \App\Enums\ActivityStatus::PENDING => 'fas fa-hourglass-start',
                                    \App\Enums\ActivityStatus::FINISHED => 'fas fa-check',
                                    \App\Enums\ActivityStatus::FINISHED_LATE => 'fas fa-hourglass-end',
                                    \App\Enums\ActivityStatus::LATE => 'fas fa-hourglass-end',
                                    \App\Enums\ActivityStatus::PAUSED => 'fas fa-pause',
                                    \App\Enums\ActivityStatus::CREATED_BY_USER => 'fas fa-user',
                                    \App\Enums\ActivityStatus::CANCELLED => 'fas fa-ban',
                                };
                                $iconColor = match (\App\Enums\ActivityStatus::from($activity->status)) {
                                    \App\Enums\ActivityStatus::PENDING => 'bg-warning',
                                    \App\Enums\ActivityStatus::FINISHED => 'bg-success',
                                    \App\Enums\ActivityStatus::FINISHED_LATE => 'bg-success',
                                    \App\Enums\ActivityStatus::LATE => 'bg-danger',
                                    \App\Enums\ActivityStatus::PAUSED => 'bg-warning',
                                    \App\Enums\ActivityStatus::CREATED_BY_USER => 'bg-success',
                                    \App\Enums\ActivityStatus::CANCELLED => 'bg-danger',
                                };
                            @endphp
                            <x-timeline-item :icon="$icon" :icon-color="$iconColor" :tooltip="__('messages.' . $activity->status)">
                                <x-slot name="header">
                                    <a href="{{ route('show-user-details', $activity->user) }}">{{ $activity->user->employee->full_name }}
                                    </a>
                                </x-slot>

                                <x-slot name="time">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    <span class="text-muted">
                                        {{ \Carbon\Carbon::parse($activity->date)->format('d-m-Y') }}
                                        {{ ' | ' }}
                                        {{ \Carbon\Carbon::parse($activity->start_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($activity->end_time)->format('h:i A') }}
                                    </span>
                                </x-slot>

                                {{ $activity->description }}

                                <x-slot name="footer">
                                    <a href="{{ route('activities.show', $activity) }}" class="btn btn-info btn-sm">Ver
                                        actividad</a>
                                </x-slot>
                            </x-timeline-item>
                        @endforeach
                    @endforeach

                    <x-timeline-item :last="$activities->last()" icon="fas fa-clock" iconColor="bg-gray" />
                </x-timeline>
            </div>

        @endif
    </div>
@stop
