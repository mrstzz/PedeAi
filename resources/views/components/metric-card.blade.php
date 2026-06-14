@props([
    'label',
    'value',
    'description' => null,
    'icon' => 'chart-bar',
    'accent' => 'text-primary bg-primary/10 ring-primary/15',
])

<article {{ $attributes->class(['rounded-lg border border-base-300/80 bg-base-100 p-5 shadow-sm transition duration-200 ease-out hover:-translate-y-0.5 hover:border-primary/25 hover:shadow-md']) }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm font-medium text-base-content/70">{{ $label }}</p>
            <p class="mt-2 truncate text-3xl font-bold tracking-normal text-neutral sm:text-4xl">{{ $value }}</p>
        </div>

        <x-icon-mark :icon="$icon" :accent="$accent" class="size-7" />
    </div>

    @if ($description)
        <p class="mt-4 border-t border-base-300/70 pt-3 text-sm text-base-content/65">{{ $description }}</p>
    @endif
</article>
