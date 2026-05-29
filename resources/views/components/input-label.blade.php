@props(['value'])

<label {{ $attributes->class(['label text-sm font-medium text-base-content']) }}>
    {{ $value ?? $slot }}
</label>
