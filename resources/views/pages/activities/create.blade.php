@extends('layouts.app')

@section('title', 'Actividades')

@section('header')
    <h1>Actividades</h1>
@stop

@section('content')
    <div class="row justify-content-center align-items-center">
        <div class="col-md-8">
            <x-adminlte-card title="Registrar actividad" theme="info" icon="fas fa-clipboard-list" maximizable>
                <form class="row" action="{{ route('activities.store') }}" method="POST">
                    @csrf
                    <x-adminlte-textarea min="5" max="255" name="description" label="Descripción"
                        fgroup-class="col-md-6">{{ old('description') }}</x-adminlte-textarea>
                    <x-adminlte-input value="{{ old('date') }}" name="date" label="Fecha" type="date"
                        fgroup-class="col-md-6" />
                    <x-adminlte-input value="{{ old('start_time') }}" name="start_time" label="Hora de inicio"
                        type="time" fgroup-class="col-md-6" />
                    <x-adminlte-input value="{{ old('end_time') }}" name="end_time" label="Hora de finalización"
                        type="time" fgroup-class="col-md-6" />
                    <x-adminlte-textarea value="{{ old('observations') }}" name="observations" label="Observaciones"
                        fgroup-class="col-md-12">{{ old('observations') }}</x-adminlte-textarea>
                    <x-adminlte-button theme="info" class="btn-flat w-100" type="submit" label="Guardar"
                        icon="fas fa-save" />
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
