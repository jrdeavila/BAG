@extends('layouts.app')

@section('title', 'Detalles de la actividad')

@section('header')
    <h1>Detalles de la actividad</h1>
@stop


@section('content')
    <div class="row">
        <div class="col-md-12">
            @foreach (['warning', 'success', 'info', 'error'] as $type)
                @if (session()->has($type))
                    <x-adminlte-alert :title="session($type)" theme="{{ $type }}" dismissable />
                @endif
            @endforeach
        </div>
        <div class="col-md-12">
            <div class="row flex-row-reverse">
                <div class="col-md-1 mb-3">
                    <form action="{{ route('activities.finish', $activity->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <x-adminlte-button label="Terminar" icon="fas fa-check" theme="success" class="w-100"
                            type="submit" />
                    </form>
                </div>
                @can('edit-activity')
                    <div class="col-md-1 mb-3">
                        <x-adminlte-button label="Editar" icon="fas fa-edit"
                            onclick="window.location='{{ route('activities.edit', $activity->id) }}';" theme="info"
                            class="w-100" />
                    </div>
                @endcan
                @can('delete-activity')
                    <div class="col-md-1 mb-3">
                        <x-adminlte-button label="Eliminar" icon="fas fa-trash" data-toggle="modal"
                            data-target="#modal-delete-activity-{{ $activity->id }}" theme="danger" class="w-100" />
                        <x-adminlte-modal id="modal-delete-activity-{{ $activity->id }}" title="Eliminar actividad"
                            theme="danger">
                            <p>Esta seguro que desea eliminar la actividad. <strong>Esta operación es irreversible</strong>
                            </p>
                            <p>¿Desea eliminar la actividad?</p>
                            <x-slot name="footerSlot">
                                <form action="{{ route('activities.destroy', $activity->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-adminlte-button theme="danger" label="Eliminar" icon="fas fa-trash"
                                        data-bs-dismiss="modal" type="submit" />
                                </form>
                            </x-slot>
                        </x-adminlte-modal>
                    </div>
                @endcan
            </div>
            <hr>
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

        @can('show-activity-owner')
            <div class="col-md-6">
                <x-users.user-details-card :user="$activity->user" />
            </div>
        @endcan

    </div>
@stop
