@props(['user'])

<x-adminlte-card title="Informacion del empleado" theme="primary" icon="fas fa-clipboard-list" maximizable>
    <dl class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-start align-items-center mb-3">
                <img class="img-circle elevation-2 float-left mr-2" width="80px" height="80px"
                    src="{{ $user->adminlte_image() }}" alt="User avatar: {{ $user->name }}">
                <div class="user-block">
                    <span class="username">
                        @can('show-activity-owner')
                            <a href="{{ route('show-user-details', $user) }}">
                                {{ $user->employee->full_name }}
                            </a>
                        @else
                            {{ $user->employee->full_name }}
                        @endcan

                    </span>
                    <span class="description">
                        {{ $user->employee->job->name }}
                    </span>
                </div>
            </div>
        </div>
        <dt class="col-sm-4">Nombre:</dt>
        <dd class="col-sm-8">{{ $user->employee->full_name }}</dd>

        <dt class="col-sm-4">Correo:</dt>
        <dd class="col-sm-8">{{ $user->employee->email }}</dd>

        <dt class="col-sm-4">Cargo:</dt>
        <dd class="col-sm-8">{{ $user->employee->job->name }}</dd>

        <dt class="col-sm-4">Documento:</dt>
        <dd class="col-sm-8">{{ $user->employee->document_number }}</dd>
    </dl>
</x-adminlte-card>
