@props(['active'])

@php
$classes = ($active ?? false)
            ? 'btn btn-sm btn-soft btn-primary'
            : 'btn btn-sm btn-ghost';
@endphp

<a {{ $attributes->class([$classes]) }}>
    {{ $slot }}
</a>
