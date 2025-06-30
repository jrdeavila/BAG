@extends('layouts.app')

@section('title', 'Reportes')

@section('header')
    <h1>Reportes</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-adminlte-card title="Filtros" theme="info" icon="fas fa-filter">
                <form class="row" action="{{ route('reports.index') }}" method="GET">
                    @csrf
                    @role(['superadmin', 'admin'])
                        <div class="col-md-4">
                            <x-adminlte-input value="{{ old('user_dni', request('user_dni')) }}" name="user_dni"
                                label="Documento del empleado" type="number" fgroup-class="col-md-12" />
                        </div>
                    @endrole
                    <x-adminlte-input value="{{ old('start_date', request('start_date')) }}" name="start_date"
                        label="Fecha de inicio" type="date" fgroup-class="col-md-2" />
                    <x-adminlte-input value="{{ old('end_date', request('end_date')) }}" name="end_date"
                        label="Fecha de finalización" type="date" fgroup-class="col-md-2" />
                    <x-adminlte-input value="{{ old('start_time') }}" name="start_time" label="Hora de inicio"
                        type="time" fgroup-class="col-md-2" />
                    <x-adminlte-input value="{{ old('end_time') }}" name="end_time" label="Hora de finalización"
                        type="time" fgroup-class="col-md-2" />
                    <div class="col-md-2">
                        <x-adminlte-button theme="info" class="btn-flat w-100" type="submit" label="Filtrar"
                            icon="fas fa-filter" />
                    </div>
                    @if (count(request()->all()) > 0)
                        <div class="col-md-2">
                            <x-adminlte-button theme="info" class="btn-flat w-100" type="button" label="Limpiar"
                                icon="fas fa-eraser" onclick="window.location='{{ route('reports.index') }}';" />
                        </div>
                    @endif
                </form>
            </x-adminlte-card>
        </div>
        <div class="col-md-8">
            <div class="row">
                @foreach ($activities as $activity)
                    <div class="col-md-4 col-sm-6">
                        <x-activities.activity-card :activity="$activity" :with-user-details="true" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    </div>
@stop
