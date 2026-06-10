@extends('adminlte::page')

@section('title', 'Configuración de acceso')

@section('content_header')
    <h1>Configuración de acceso</h1>
@stop

@section('content')
    @foreach (['success' => ['success', 'fas fa-check'], 'warning' => ['warning', 'fas fa-exclamation-triangle']] as $key => $cfg)
        @if (session($key))
            <x-adminlte-alert theme="{{ $cfg[0] }}" icon="{{ $cfg[1] }}" dismissable>
                {{ session($key) }}
            </x-adminlte-alert>
        @endif
    @endforeach

    @if ($errors->any())
        <x-adminlte-alert title="Error" theme="danger" dismissable>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-adminlte-alert>
    @endif

    {{-- ============ AREAS Y RESPONSABLES ============ --}}
    <x-adminlte-card title="Áreas habilitadas y responsables" theme="info" icon="fas fa-sitemap">
        <p class="text-muted">
            Solo los funcionarios de áreas <strong>habilitadas</strong> (o los funcionarios especiales) pueden ingresar.
            Cada área puede tener uno o más responsables que gestionan las actividades de su área.
        </p>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Área</th>
                        <th class="text-center" style="width: 140px;">Estado</th>
                        <th>Responsables</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($areas as $area)
                        @php $enabled = in_array($area->id, $enabledAreaIds); @endphp
                        <tr>
                            <td><strong>{{ $area->name }}</strong></td>
                            <td class="text-center">
                                <form action="{{ route('admin.areas.toggle', $area->id) }}" method="POST">
                                    @csrf
                                    @if ($enabled)
                                        <button type="submit" class="btn btn-success btn-sm btn-flat" title="Deshabilitar">
                                            <i class="fas fa-check"></i> Habilitada
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-outline-secondary btn-sm btn-flat" title="Habilitar">
                                            <i class="fas fa-times"></i> Deshabilitada
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td>
                                @if ($enabled)
                                    @foreach ($responsibles[$area->id] ?? [] as $resp)
                                        <span class="badge bg-primary mr-1 mb-1" style="font-size: .85rem;">
                                            {{ optional(optional($resp->user)->employee)->full_name ?? ('Usuario #' . $resp->user_id) }}
                                            <form action="{{ route('admin.areas.responsibles.remove', [$area->id, $resp->user_id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-sm p-0 text-white" title="Quitar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </span>
                                    @endforeach
                                    <form action="{{ route('admin.areas.responsibles.add', $area->id) }}" method="POST"
                                        class="form-inline mt-1">
                                        @csrf
                                        <input type="text" name="user_dni" class="form-control form-control-sm mr-1"
                                            placeholder="Documento del funcionario" required>
                                        <button type="submit" class="btn btn-info btn-sm btn-flat">
                                            <i class="fas fa-user-plus"></i> Agregar
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-adminlte-card>

    {{-- ============ FUNCIONARIOS ESPECIALES ============ --}}
    <x-adminlte-card title="Funcionarios especiales" theme="success" icon="fas fa-user-shield">
        <p class="text-muted">Acceso permitido aunque su área no esté habilitada.</p>
        <form action="{{ route('admin.special-users.add') }}" method="POST" class="form-inline mb-3">
            @csrf
            <input type="text" name="user_dni" class="form-control form-control-sm mr-2"
                placeholder="Documento del funcionario" required>
            <input type="text" name="reason" class="form-control form-control-sm mr-2"
                placeholder="Razón (opcional)">
            <button type="submit" class="btn btn-success btn-sm btn-flat">
                <i class="fas fa-plus"></i> Agregar especial
            </button>
        </form>
        @if ($specials->isEmpty())
            <p class="text-muted mb-0">No hay funcionarios especiales.</p>
        @else
            <table class="table table-sm table-hover">
                <thead>
                    <tr><th>Funcionario</th><th>Documento</th><th>Razón</th><th class="text-center">Acción</th></tr>
                </thead>
                <tbody>
                    @foreach ($specials as $sp)
                        <tr>
                            <td>{{ optional(optional($sp->user)->employee)->full_name ?? ('Usuario #' . $sp->user_id) }}</td>
                            <td>{{ optional(optional($sp->user)->employee)->document_number ?? '—' }}</td>
                            <td>{{ $sp->reason ?? '—' }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.special-users.remove', $sp->user_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm btn-flat">
                                        <i class="fas fa-trash"></i> Quitar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-adminlte-card>

    {{-- ============ BLOQUEOS INDIVIDUALES ============ --}}
    <x-adminlte-card title="Bloqueos individuales" theme="danger" icon="fas fa-user-lock">
        <p class="text-muted">Niega el acceso a un funcionario aunque su área esté habilitada (no aplica a superadmin).</p>
        <form action="{{ route('admin.blocked-users.add') }}" method="POST" class="form-inline mb-3">
            @csrf
            <input type="text" name="user_dni" class="form-control form-control-sm mr-2"
                placeholder="Documento del funcionario" required>
            <input type="text" name="reason" class="form-control form-control-sm mr-2"
                placeholder="Razón (opcional)">
            <button type="submit" class="btn btn-danger btn-sm btn-flat">
                <i class="fas fa-ban"></i> Bloquear
            </button>
        </form>
        @if ($blocked->isEmpty())
            <p class="text-muted mb-0">No hay funcionarios bloqueados.</p>
        @else
            <table class="table table-sm table-hover">
                <thead>
                    <tr><th>Funcionario</th><th>Documento</th><th>Razón</th><th class="text-center">Acción</th></tr>
                </thead>
                <tbody>
                    @foreach ($blocked as $bl)
                        <tr>
                            <td>{{ optional(optional($bl->user)->employee)->full_name ?? ('Usuario #' . $bl->user_id) }}</td>
                            <td>{{ optional(optional($bl->user)->employee)->document_number ?? '—' }}</td>
                            <td>{{ $bl->reason ?? '—' }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.blocked-users.remove', $bl->user_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-secondary btn-sm btn-flat">
                                        <i class="fas fa-unlock"></i> Desbloquear
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-adminlte-card>
@stop
