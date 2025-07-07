@props(['status' => null])

@php
    $status = strtolower($status);
    $classes = 'badge bg-';
    $label = '';
    $icon = '';
    switch ($status) {
        case 'low':
            $classes .= 'warning';
            $label = 'Bajo';
            break;

        case 'high':
            $classes .= 'success';
            $label = 'Alto';
            break;

        case 'medium':
            $classes .= 'info';
            $label = 'Medio';
            break;

        case 'created_by_user':
            $classes .= 'success';
            $label = 'Autoasignado';
            $icon = 'fas fa-user';
            break;
        case 'pending':
            $classes .= 'warning';
            $label = 'Pendiente';
            $icon = 'fas fa-hourglass-start';
            break;
        case 'paused':
            $classes .= 'warning';
            $label = 'Pausado';
            $icon = 'fas fa-pause';
            break;
        case 'finished':
            $classes .= 'success';
            $label = 'Terminado';
            $icon = 'fas fa-check';
            break;
        case 'cancelled':
            $classes .= 'danger';
            $label = 'Cancelado';
            $icon = 'fas fa-ban';
            break;
        case 'late':
            $classes .= 'danger';
            $label = 'Atrasado';
            $icon = 'fas fa-hourglass-end';
            break;
        case 'finished_late':
            $classes .= 'success';
            $label = 'Terminado Atrasado';
            $icon = 'fas fa-hourglass-end';
            break;
    }
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon != '')
        <i class="{{ $icon }}"></i>
    @endif
    {{ $label }}
</span>
