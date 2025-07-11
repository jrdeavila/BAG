@props(['activity', 'withUserDetails' => false, 'redirect' => true])

<x-adminlte-card title="{{ \Carbon\Carbon::parse($activity->date)->format('d-m-Y') }}" theme="light"
    icon="fas fa-clipboard-list" :onclick="$redirect ? 'window.location=\'' . route('activities.show', $activity->id) . '\';' : ''">
    <x-slot name="toolsSlot">
        <x-badge status="{{ $activity->status }}" />
    </x-slot>
    <dl class="row">
        @if ($withUserDetails)
            <div class="col-md-12">
                <div class="d-flex justify-content-start align-items-center">
                    <img class="img-circle elevation-2 float-left mr-2" src="{{ $activity->user->adminlte_image() }}"
                        alt="User avatar: {{ $activity->user->name }}" style="width: 80px; height: 80px;">
                    <div class="d-flex flex-column ml-2">
                        <span class="username">
                            @can('show-activity-owner')
                                <a href="{{ route('show-user-details', $activity->user) }}">
                                    {{ $activity->user->employee->full_name }}
                                </a>
                            @else
                                {{ $activity->user->employee->full_name }}
                            @endcan
                        </span>

                        <span class="description">
                            {{ $activity->user->employee->job->name }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
        <dt class="col-sm-4">Prioridad:</dt>
        <dd class="col-sm-8">
            <x-badge status="{{ $activity->priority }}" />
        </dd>
        @if (Auth::id() !== $activity->created_by)
            <dt class="col-sm-4">Creado por:</dt>
            <dd class="col-sm-8">
                @can('show-activity-owner')
                    <a href="{{ route('show-user-details', $activity->createdBy) }}">
                        {{ $activity->createdBy->employee->full_name }}
                    </a>
                @else
                    {{ $activity->createdBy->employee->full_name }}
                @endcan
            </dd>
        @endif
        <dt class="col-sm-4">Descripción:</dt>
        <dd class="col-sm-8">{{ $activity->description }}</dd>

        <dt class="col-sm-4">Hora de inicio:</dt>
        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($activity->start_time)->format('h:i A') }}</dd>
        <dt class="col-sm-4">Hora de finalización:</dt>
        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($activity->end_time)->format('h:i A') }}</dd>
    </dl>
</x-adminlte-card>
