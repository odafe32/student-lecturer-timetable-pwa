@props(['route', 'icon', 'label'])

@php
    $isActive = request()->routeIs($route);
@endphp

<li class="{{ $isActive ? 'active' : '' }}">
    <a href="{{ route($route) }}">
        <i class="bi {{ $icon }}"></i>
        <span>{{ $label }}</span>
    </a>
</li>
