@props(['user'])

<x-adminlte-card title="Informacion del empleado" theme="primary" icon="fas fa-clipboard-list" maximizable>
    <dl class="row">
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
