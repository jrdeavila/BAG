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
        </div>
        <div class="col-md-8">
            <x-adminlte-card title="Editar actividad" theme="info" icon="fas fa-clipboard-list" maximizable>
                <form class="row" action="{{ route('activities.update', $activity) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $activity->id }}">
                    <x-adminlte-textarea min="5" max="255" name="description" label="Descripción"
                        fgroup-class="col-md-6">{{ old('description', $activity->description) }}</x-adminlte-textarea>
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
