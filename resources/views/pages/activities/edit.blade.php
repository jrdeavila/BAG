@extends('layouts.app')

@section('title', 'Actividades')

@section('header')
    <h1>Actividades</h1>
@stop

@section('content')
    <div class="row justify-content-center align-items-center">
        <div class="col-md-8">
            @foreach (['warning', 'success', 'info', 'error'] as $type)
                @if (session()->has($type))
                    <x-adminlte-alert :title="session($type)" theme="{{ $type }}" dismissable />
                @endif
            @endforeach
            @if ($errors->any())
                <x-adminlte-alert title="Error en el formulario" theme="danger" dismissable>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-adminlte-alert>
            @endif
        </div>
        @can('assign-activity')
            <div class="col-md-8">
                @if (is_null($user))
                    <x-adminlte-card title="Buscar empleado" theme="info" icon="fas fa-clipboard-list" maximizable>
                        <form class="row" action="{{ route('activities.edit', $activity) }}" method="GET">
                            @csrf
                            <input type="hidden" name="remove_user" value="{{ request('remove_user') }}">
                            <x-adminlte-input value="{{ old('user_dni', request('user_dni')) }}" name="user_dni"
                                label="Documento del empleado" type="number" fgroup-class="col-md-12" />
                            <x-adminlte-button theme="info" class="btn-flat" type="submit" label="Buscar"
                                icon="fas fa-search" />
                        </form>
                    </x-adminlte-card>
                @else
                    <div class="d-flex flex-row-reverse mb-3">
                        <x-adminlte-button theme="danger" class="btn-flat" type="button" label="Cambiar empleado"
                            icon="fas fa-eraser" class="ml-1"
                            onclick="window.location='{{ route('activities.edit', $activity) }}?remove_user=true';" />
                        @if ($activity->user_id != $user->id)
                            <x-adminlte-button theme="info" class="btn-flat" type="button" label="Dejar empleado anterior"
                                icon="fas fa-user"
                                onclick="window.location='{{ route('activities.edit', $activity) }}?remove_user=false';" />
                        @endif
                    </div>
                    <x-users.user-details-card :user="$user" />
                @endif
            </div>
        @endcan
        <div class="col-md-8">
            <x-adminlte-card title="Editar actividad" theme="info" icon="fas fa-clipboard-list" maximizable>
                <form class="row" action="{{ route('activities.update', $activity) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <x-adminlte-textarea min="5" max="255" name="description" label="Descripción"
                        fgroup-class="col-md-6">{{ old('description', $activity->description) }}</x-adminlte-textarea>
                    @can('assign-activity')
                        <input type="hidden" name="user_id"
                            value="{{ request()->get('remove_user') ? $user?->id ?? $activity->user_id : $activity->user_id }}">
                        <x-adminlte-select name="status" label="*Estado" fgroup-class="col-md-6">
                            @foreach (\App\Enums\ActivityStatus::cases() as $key => $status)
                                <option value="{{ $status->value }}"
                                    {{ old('status', $activity->status) == $status->value ? 'selected' : '' }}>
                                    {{ __('messages.' . $status->name) }}</option>
                            @endforeach
                        </x-adminlte-select>
                        <x-adminlte-select name="priority" label="*Prioridad" fgroup-class="col-md-6">
                            @foreach (\App\Enums\ActivityPriority::cases() as $key => $priority)
                                <option value="{{ $priority->value }}"
                                    {{ old('priority', $activity->priority) == $priority->value ? 'selected' : '' }}>
                                    {{ __('messages.' . $priority->name) }}</option>
                            @endforeach
                        </x-adminlte-select>
                    @endcan
                    <input type="hidden" name="id" value="{{ $activity->id }}">

                    <x-adminlte-input value="{{ old('date', \Carbon\Carbon::parse($activity->date)->format('Y-m-d')) }}"
                        name="date" label="Fecha" type="date" fgroup-class="col-md-6" />
                    <x-adminlte-input value="{{ old('start_time', $activity->start_time) }}" name="start_time"
                        label="Hora de inicio" type="time" fgroup-class="col-md-6" />
                    <x-adminlte-input value="{{ old('end_time', $activity->end_time) }}" name="end_time"
                        label="Hora de finalización" type="time" fgroup-class="col-md-6" />
                    <x-adminlte-textarea name="observations" label="Observaciones"
                        fgroup-class="col-md-12">{{ old('observations', $activity->observations) }}</x-adminlte-textarea>
                    <x-adminlte-button theme="info" class="btn-flat w-100" type="submit" label="Guardar"
                        icon="fas fa-save" />
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
