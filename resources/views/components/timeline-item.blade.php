@props([
    'header' => '',
    'time' => '',
    'footer' => '',
    'icon' => 'fas fa-info',
    'iconColor' => 'bg-blue',
    'tooltip' => null,
    'last' => false,
])

<div>
    @if ($last)
        <i class="fas fa-clock bg-gray"></i>
    @else
        <i class="{{ $icon }} {{ $iconColor }}"
            {{ $tooltip ? 'data-toggle="tooltip" title="' . $tooltip . '"' : '' }}></i>
        <div class="timeline-item">
            @if ($time)
                <span class="time">
                    {{ $time }}
                </span>
            @endif
            @if ($header)
                <h3 class="timeline-header">{{ $header }}</h3>
            @endif
            @if ($slot)
                <div class="timeline-body">
                    {{ $slot }}
                </div>
            @endif
            @if ($footer)
                <div class="timeline-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    @endif
</div>
