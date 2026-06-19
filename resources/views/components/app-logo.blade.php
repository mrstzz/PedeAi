@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="PedeAi" {{ $attributes->class(['app-sidebar-brand font-bold [&_span]:!text-[var(--color-sidebar-content)] [&_div]:!text-[var(--color-sidebar-content)]']) }}>
        <x-slot name="logo" class="flex aspect-square size-10 items-center justify-center rounded-lg bg-white p-1 shadow-sm ring-1 ring-white/20">
            <x-app-logo-icon class="size-9" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="PedeAi" {{ $attributes->class(['font-bold [&_span]:!text-[var(--color-base-content)] [&_div]:!text-[var(--color-base-content)]']) }}>
        <x-slot name="logo" class="flex aspect-square size-10 items-center justify-center rounded-lg bg-white p-1 shadow-sm ring-1 ring-base-300">
            <x-app-logo-icon class="size-9" />
        </x-slot>
    </flux:brand>
@endif