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
                    <x-adminlte-alert theme="{{ $type }}" dismissable>
                        {{ session($type) }}
                    </x-adminlte-alert>
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
                        <form class="row" action="{{ route('activities.create') }}" method="GET">
                            @csrf
                            <x-adminlte-input value="{{ old('user_dni', request('user_dni')) }}" name="user_dni"
                                label="Documento del empleado" type="number" fgroup-class="col-md-12" />
                            <x-adminlte-button theme="info" class="btn-flat" type="submit" label="Buscar"
                                icon="fas fa-search" />
                        </form>
                    </x-adminlte-card>
                @else
                    <div class="d-flex flex-row-reverse mb-3">
                        <x-adminlte-button theme="danger" class="btn-flat" type="button" label="Cambiar empleado"
                            icon="fas fa-eraser" onclick="window.location='{{ route('activities.create') }}';" />
                    </div>
                    <x-users.user-details-card :user="$user" />
                @endif
            </div>
        @endcan
        <div class="col-md-8">
            <x-adminlte-card title="Registrar actividad" theme="info" icon="fas fa-clipboard-list" maximizable>
                <form class="row" action="{{ route('activities.store') }}" method="POST">
                    @csrf
                    <x-adminlte-textarea min="6" max="255" name="description" label="*Descripción"
                        fgroup-class="col-md-6">{{ old('description') }}</x-adminlte-textarea>
                    @can('assign-activity')
                        <input type="hidden" name="user_id" value="{{ $user?->id }}">
                        <input type="hidden" name="status" value="{{ \App\Enums\ActivityStatus::PENDING->value }}">

                        <x-adminlte-select name="priority" label="*Prioridad" fgroup-class="col-md-6">
                            @foreach (\App\Enums\ActivityPriority::cases() as $key => $priority)
                                <option value="{{ $priority->value }}">{{ __('messages.' . $priority->name) }}</option>
                            @endforeach
                        </x-adminlte-select>
                    @endcan

                    <x-adminlte-input value="{{ old('date') }}" name="date" label="*Fecha" type="date"
                        fgroup-class="col-md-6" />
                    <x-adminlte-input value="{{ old('start_time') }}" name="start_time" label="*Hora de inicio"
                        type="time" fgroup-class="col-md-6" />
                    <x-adminlte-input value="{{ old('end_time') }}" name="end_time" label="*Hora de finalización"
                        type="time" fgroup-class="col-md-6" />
                    <x-adminlte-textarea value="{{ old('observations') }}" name="observations" label="Observaciones"
                        fgroup-class="col-md-6">{{ old('observations') }}</x-adminlte-textarea>
                    <x-adminlte-button theme="info" class="btn-flat" type="submit" label="Guardar" icon="fas fa-save" />
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
