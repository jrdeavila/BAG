@props(['text' => '', 'tooltip' => null])

<div class="time-label" {{ $tooltip ? 'data-toggle=tooltip title=' . $tooltip : '' }}>
    <span {{ $attributes }}>{{ $text }}</span>
</div>
