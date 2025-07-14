@extends('layouts.app')

@section('title', 'Actividades')

@section('header')
    <h1>Actividades</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-adminlte-card title="Filtros" theme="info" icon="fas fa-filter">
                <form action="{{ route('activities.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <x-adminlte-input value="{{ old('date', request('date')) }}" name="date" label="Fecha"
                                type="date" fgroup-class="col-md-12" />
                        </div>
                        <div class="col-md-3">
                            <x-adminlte-input value="{{ old('start_time', request('start_time')) }}" name="start_time"
                                label="Hora de inicio" type="time" fgroup-class="col-md-12" />
                        </div>
                        <div class="col-md-3">
                            <x-adminlte-input value="{{ old('end_time', request('end_time')) }}" name="end_time"
                                label="Hora de finalización" type="time" fgroup-class="col-md-12" />
                        </div>
                        <div class="col-md-2 d-flex align-items-end pb-3">
                            <x-adminlte-button theme="info" class="btn-flat w-100" type="submit" label="Filtrar"
                                icon="fas fa-filter" />

                        </div>
                        @if (count(request()->all()) > 0)
                            <div class="col-md-2 d-flex align-items-end pb-3">
                                <x-adminlte-button theme="info" class="btn-flat w-100" type="button" label="Limpiar"
                                    icon="fas fa-eraser" onclick="window.location='{{ route('activities.index') }}';" />
                            </div>
                        @endif
                    </div>
                </form>
            </x-adminlte-card>
        </div>

        <div class="col-md-8">
            <div class="d-none d-lg-block">
                <x-adminlte-card title="Listado de actividades" theme="light" icon="fas fa-clipboard-list">
                    <x-slot name="toolsSlot">
                        @can('create-activity')
                            <x-adminlte-button label="Registrar actividad" icon="fas fa-clipboard-list"
                                onclick="window.location='{{ route('activities.create') }}';" theme="info" class="w-100" />
                        @endcan
                    </x-slot>
                    @php
                        if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin')) {
                            $heads = [
                                'Descripción',
                                'Estado',
                                'Prioridad',
                                ['label' => 'Fecha', 'width' => 10, 'sortable' => true],
                                'Empleado',
                                'Hora de inicio',
                                'Hora de finalización',
                                'Acciones',
                            ];
                        } else {
                            $heads = [
                                'Descripción',
                                'Estado',
                                'Prioridad',
                                ['label' => 'Fecha', 'width' => 10, 'sortable' => true],
                                'Hora de inicio',
                                'Hora de finalización',
                                'Acciones',
                            ];
                        }
                        $config = [
                            'data' => $activities,
                            'order' => [[1, 'asc']],
                            'columns' => [null, null, null, null, null, ['orderable' => false]],
                        ];
                    @endphp
                    <x-adminlte-datatable id="table-activities" :heads="$heads" :config="$config">
                        @foreach ($activities as $activity)
                            <tr>
                                <td style="max-width: 200px">
                                    <span class="text-wrap">
                                        {{ $activity->description }}
                                    </span>
                                </td>
                                <td>
                                    <x-badge status="{{ $activity->status }}" />
                                </td>
                                <td>
                                    <x-badge status="{{ $activity->priority }}" />
                                </td>
                                <td>{{ \Carbon\Carbon::parse($activity->date)->format('d-m-Y') }}</td>
                                @role(['superadmin', 'admin'])
                                    <td>
                                        <a href="{{ route('show-user-details', $activity->user) }}">
                                            {{ $activity->user->employee->full_name }}
                                        </a>
                                    </td>
                                @endrole
                                <td>{{ \Carbon\Carbon::parse($activity->start_time)->format('h:i A') }}</td>
                                <td>{{ \Carbon\Carbon::parse($activity->end_time)->format('h:i A') }}</td>
                                <td>
                                    <div class="btn-group">
                                        @can('view-activity')
                                            <x-adminlte-button label="Ver" icon="fas fa-eye"
                                                onclick="window.location='{{ route('activities.show', $activity->id) }}';"
                                                theme="info" class="w-100 btn-flat" />
                                        @endcan
                                        @can('edit-activity')
                                            <x-adminlte-button label="Editar" icon="fas fa-edit"
                                                onclick="window.location='{{ route('activities.edit', $activity->id) }}';"
                                                theme="info" class="w-100 btn-flat" />
                                        @endcan
                                        @can('delete-activity')
                                            <x-adminlte-button label="Eliminar" icon="fas fa-trash" data-toggle="modal"
                                                data-target="#modal-delete-activity-{{ $activity->id }}" theme="danger"
                                                class="w-100 btn-flat" />
                                            <x-adminlte-modal id="modal-delete-activity-{{ $activity->id }}"
                                                title="Eliminar actividad" theme="danger">
                                                <p>Esta seguro que desea eliminar la actividad. <strong>Esta operación es
                                                        irreversible</strong></p>
                                                <p>¿Desea eliminar la actividad?</p>
                                                <x-slot name="footerSlot">
                                                    <form action="{{ route('activities.destroy', $activity->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-adminlte-button theme="danger" label="Eliminar" icon="fas fa-trash"
                                                            data-bs-dismiss="modal" type="submit" />
                                                    </form>
                                                </x-slot>
                                            </x-adminlte-modal>
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </x-adminlte-card>
                {{ $activities->links('custom.pagination') }}
            </div>
            <div class="d-lg-none row">
                <div class="col-md-12 justify-content-end mb-3">
                    {{ $activities->withQueryString()->links('custom.pagination') }}
                </div>
                @foreach ($activities as $activity)
                    <div class=" col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <x-activities.activity-card :activity="$activity" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="d-md-none d-block position-fixed" style="bottom: 20px; right: 20px;">
        <x-adminlte-button label="Registrar actividad" icon="fas fa-clipboard-list"
            onclick="window.location='{{ route('activities.create') }}';" theme="info" class="w-100" />
    </div>
@stop
