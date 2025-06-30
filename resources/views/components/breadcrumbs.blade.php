@php
    $segments = request()->segments();
    $url = url('/');
@endphp

<nav aria-label="breadcrumb">
    <ol class="breadcrumb flex text-sm text-gray-600 space-x-1">
        <li>
            <a href="{{ url('/') }}" class="hover:underline text-blue-600">{{ __('messages.home') }}</a>
            <span class="mx-1">/</span>
        </li>
        @foreach ($segments as $key => $segment)
            @php
                $url .= '/' . $segment;
                $label = ucwords(str_replace(['-', '_'], ' ', $segment));
            @endphp

            @if (!is_numeric($label))
                @if ($key !== array_key_last($segments))
                    <li>
                        <a href="{{ $url }}"
                            class="hover:underline text-blue-600">{{ __('messages.' . $label) }}</a>
                        <span class="mx-1">/</span>
                    </li>
                @else
                    <li class="font-semibold text-gray-900">{{ __('messages.' . $label) }}</li>
                @endif
            @endif
        @endforeach
    </ol>
</nav>
