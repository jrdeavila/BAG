@extends('layouts.app')

@section('title', 'Actividades')

@section('header')
    <h1>Actividades</h1>
@stop

@section('content')
    <div class="row justify-content-center align-items-center">
        <div class="col-md-8">
            @foreach (['warning', 'success', 'info', 'error', 'status'] as $type)
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

        <div class="col-md-8">
            <x-adminlte-card title="Registrar actividad" theme="info" icon="fas fa-clipboard-list" maximizable>
                <form class="row" action="{{ route('activities.store') }}" method="POST">
                    @csrf
                    @can('assign-activity')
                        <x-adminlte-select name="user_id" label="*Empleado" fgroup-class="col-md-6">
                            <option value="">Seleccione un empleado</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employee->full_name }} ({{ $employee->employee->noDocumento }})
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    @endcan
                    <x-adminlte-textarea maxlength="255" minlength="5" name="description" label="*Descripción"
                        fgroup-class="col-md-6">{{ old('description') }}</x-adminlte-textarea>
                    @can('assign-activity')


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
                        maxlength="1000" rows="5"
                        fgroup-class="col-md-6">{{ old('observations') }}</x-adminlte-textarea>
                    <div class="col-md-12">
                        <x-adminlte-button theme="info" class="btn-flat" type="submit" label="Guardar"
                            icon="fas fa-save" />
                    </div>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
