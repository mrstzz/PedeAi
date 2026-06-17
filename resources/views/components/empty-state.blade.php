@props([
    'title',
    'description' => null,
    'icon' => 'inbox',
])

<div {{ $attributes->class(['flex flex-col items-center gap-4 p-10 text-center']) }}>
    <x-icon-mark :icon="$icon" accent="text-primary" class="size-8" />
    <div>
        <h2 class="text-lg font-semibold text-neutral">{{ $title }}</h2>
        @if ($description)
            <p class="mt-1 max-w-md text-sm text-base-content/65">{{ $description }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @endif
</div>
