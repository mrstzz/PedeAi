@props([
    'post' => null
])

@php
    $method = $post ? 'POST' : 'GET';
@endphp

<form method="{{ $method }}" {{ $attributes->class(['flex flex-col gap-4']) }}>
    
    @if($method === 'POST')
        @csrf
    @endif

    {{ $slot }}
    
</form>
