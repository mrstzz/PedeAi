@props(['active'])

@php
$classes = ($active ?? false)
            ? 'btn btn-soft btn-primary w-full justify-start'
            : 'btn btn-ghost w-full justify-start';
@endphp

<a {{ $attributes->class([$classes]) }}>
    {{ $slot }}
</a>
