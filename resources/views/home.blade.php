@extends('layouts.app')

@section('title', 'Mis actividades pendientes')

@section('header')
    <h1>Mis actividades pendientes</h1>
@stop

@section('content')
    @if (count($activities) > 0)
        <div class="d-none d-md-block row">
            <div class="col-md-8">
                <x-adminlte-card title="Listado de actividades pendientes" theme="light" icon="fas fa-clipboard-list">
                    <x-adminlte-datatable id="table-activities" :heads="['Fecha', 'Actividad', 'Hora de inicio', 'Hora de finalización', 'Estado']">
                        @foreach ($activities as $activity)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($activity->date)->format('d-m-Y') }}</td>
                                <td>{{ $activity->description }}</td>
                                <td>{{ \Carbon\Carbon::parse($activity->start_time)->format('h:i A') }}</td>
                                <td>{{ \Carbon\Carbon::parse($activity->end_time)->format('h:i A') }}</td>
                                <td>
                                    <x-badge status="{{ $activity->status }}" />
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </x-adminlte-card>
                {{ $activities->links('custom.pagination') }}
            </div>
        </div>
        <div class="d-md-none row">
            {{ $activities->links('custom.pagination') }}
            @foreach ($activities as $activity)
                <div class=" col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <x-activities.activity-card :activity="$activity" />
                </div>
            @endforeach
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-md-5">
                <x-adminlte-info-box text="No hay actividades pendientes" theme="primary" icon="far fa-calendar-alt" />
            </div>
        </div>
    @endif
@endsection
