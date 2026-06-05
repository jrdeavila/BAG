@extends('adminlte::page')

@section('title', 'Asignación de roles')

@section('content_header')
    <h1>Asignación de roles</h1>
@stop

@section('content')
    @if (session('success'))
        <x-adminlte-alert theme="success" icon="fas fa-check" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if (session('warning'))
        <x-adminlte-alert theme="warning" icon="fas fa-exclamation-triangle" dismissable>
            {{ session('warning') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card title="Filtrar usuarios" theme="info" icon="fas fa-search">
        <form action="{{ route('admin.user-roles.index') }}" method="GET">
            <div class="row">
                <div class="col-md-9">
                    <x-adminlte-input name="q" value="{{ $search }}"
                        placeholder="Buscar por correo, nombre, apellido o documento"
                        fgroup-class="mb-0" />
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <x-adminlte-button theme="info" type="submit" label="Buscar" icon="fas fa-search"
                        class="btn-flat mr-2" />
                    @if ($search !== '')
                        <x-adminlte-button theme="secondary" type="button" label="Limpiar"
                            icon="fas fa-eraser" class="btn-flat"
                            onclick="window.location='{{ route('admin.user-roles.index') }}';" />
                    @endif
                </div>
            </div>
        </form>
    </x-adminlte-card>

    <x-adminlte-card title="Usuarios" theme="light" icon="fas fa-users-cog">
        @if ($users->isEmpty())
            <p class="text-muted mb-0">No se encontraron usuarios.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Correo</th>
                            <th class="text-center">Estado empleado</th>
                            @foreach ($assignableRoles as $role)
                                <th class="text-center">{{ $role }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            @php
                                $currentRoles = $userRoles[$user->id] ?? [];
                            @endphp
                            <tr>
                                <td>
                                    @if ($user->employee)
                                        <strong>{{ $user->employee->full_name }}</strong>
                                        <div class="text-muted small">Doc: {{ $user->employee->document_number ?? '—' }}</div>
                                    @else
                                        <span class="text-muted">Sin empleado vinculado</span>
                                    @endif
                                </td>
                                <td>{{ $user->correo }}</td>
                                <td class="text-center">
                                    @if ($user->employee)
                                        @if ($user->employee->status)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                            <div class="text-muted small">({{ $user->employee->getAttributes()['estado'] ?? '—' }})</div>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">—</span>
                                    @endif
                                </td>
                                @foreach ($assignableRoles as $role)
                                    @php $has = in_array($role, $currentRoles, true); @endphp
                                    <td class="text-center">
                                        <form action="{{ route('admin.user-roles.toggle', $user->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <input type="hidden" name="role" value="{{ $role }}">
                                            <input type="hidden" name="action" value="{{ $has ? 'revoke' : 'assign' }}">
                                            @if ($has)
                                                <button type="submit"
                                                    class="btn btn-success btn-sm btn-flat"
                                                    title="Quitar {{ $role }}">
                                                    <i class="fas fa-check"></i> Asignado
                                                </button>
                                            @else
                                                <button type="submit"
                                                    class="btn btn-outline-secondary btn-sm btn-flat"
                                                    title="Asignar {{ $role }}">
                                                    <i class="fas fa-times"></i> No asignado
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        @endif
    </x-adminlte-card>
@stop
