@props([
    'label',
    'value',
    'description' => null,
    'icon' => 'chart-bar',
    'accent' => 'text-primary bg-primary/10 ring-primary/15',
])

<article {{ $attributes->class(['rounded-lg border border-base-300/80 bg-base-100 p-5 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm font-medium text-base-content/60">{{ $label }}</p>
            <p class="mt-2 truncate text-3xl font-bold tracking-normal text-neutral sm:text-4xl">{{ $value }}</p>
        </div>

        <div class="grid size-11 shrink-0 place-items-center rounded-lg ring-1 {{ $accent }}">
            <flux:icon :name="$icon" class="size-5" />
        </div>
    </div>

    @if ($description)
        <p class="mt-4 border-t border-base-300/70 pt-3 text-sm text-base-content/60">{{ $description }}</p>
    @endif
</article>
