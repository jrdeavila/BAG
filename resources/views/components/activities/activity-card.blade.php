@props(['activity', 'withUserDetails' => false])

<x-adminlte-card onclick="window.location='{{ route('activities.show', $activity->id) }}';"
    title="{{ \Carbon\Carbon::parse($activity->date)->format('d-m-Y') }}" theme="info" icon="fas fa-clipboard-list">
    <dl class="row">
        @if ($withUserDetails)
            <div class="col-md-12">
                <div class="d-flex justify-content-center align-items-center">
                    <img class="img-circle elevation-2 float-left mr-2" width="80px" height="80px"
                        src="{{ $activity->user->adminlte_image() }}" alt="User avatar: {{ $activity->user->name }}">
                    <div class="user-block">
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
        <dt class="col-sm-4">Descripción:</dt>
        <dd class="col-sm-8">{{ $activity->description }}</dd>

        <dt class="col-sm-4">Hora de inicio:</dt>
        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($activity->start_time)->format('h:i A') }}</dd>
        <dt class="col-sm-4">Hora de finalización:</dt>
        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($activity->end_time)->format('h:i A') }}</dd>
    </dl>
</x-adminlte-card>
