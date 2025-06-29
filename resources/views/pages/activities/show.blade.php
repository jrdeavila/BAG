@extends('layouts.app')

@section('title', 'Detalles de la actividad')

@section('header')
    <h1>Detalles de la actividad</h1>
@stop


@section('content')
    <div class="row">
        <div class="col-md-12">
            @role(['superadmin', 'admin'])
                <div class="row flex-row-reverse">
                    @can('edit-activity')
                        <div class="col-md-1 mb-3">
                            <x-adminlte-button label="Editar" icon="fas fa-edit"
                                onclick="window.location='{{ route('activities.edit', $activity->id) }}';" theme="info"
                                class="w-100" />
                        </div>
                    @endcan
                    @can('delete-activity')
                        <div class="col-md-1 mb-3">
                            <x-adminlte-button label="Eliminar" icon="fas fa-trash" data-bs-toggle="modal"
                                data-bs-target="#modal-delete-activity-{{ $activity->id }}" theme="danger" class="w-100" />
                            <x-adminlte-modal id="modal-delete-activity-{{ $activity->id }}" title="Eliminar actividad"
                                theme="danger">
                                <x-slot name="body">
                                    <p>¿Desea eliminar la actividad?</p>
                                </x-slot>
                                <x-slot name="footerSlot">
                                    <x-adminlte-button theme="danger" label="Eliminar" icon="fas fa-trash" data-bs-dismiss="modal"
                                        onclick="window.location='{{ route('activities.destroy', $activity->id) }}';" />
                                </x-slot>
                            </x-adminlte-modal>
                        </div>
                    @endcan
                </div>
                <hr>
            @endrole
        </div>
        <div class="col-md-6">
            <x-activities.activity-card :activity="$activity" />
        </div>

        <div class="col-md-6">
            <x-adminlte-card title="Observaciones" theme="info" icon="fas fa-clipboard-list" :maximizable="$activity->observations != null">
                @if ($activity->observations == null)
                    <p>Sin observaciones</p>
                @else
                    {{ $activity->observations }}
                @endif
            </x-adminlte-card>
        </div>

        @role(['superadmin', 'admin'])
            <div class="col-md-6">
                <x-users.user-details-card :user="$activity->user" />
            </div>
        @endrole

    </div>
@stop
