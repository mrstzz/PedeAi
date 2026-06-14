@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'icon' => null,
])

<section {{ $attributes->class(['rounded-lg border border-base-300/80 bg-base-100/90 p-5 shadow-sm backdrop-blur sm:p-6']) }}>
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div class="min-w-0">
            @if ($eyebrow)
                <div class="mb-3 inline-flex items-center gap-2 rounded-md border border-secondary/25 bg-secondary/10 px-3 py-1 text-xs font-semibold uppercase tracking-normal text-secondary">
                    <span class="size-1.5 rounded-full bg-secondary"></span>
                    {{ $eyebrow }}
                </div>
            @endif

            <h1 class="text-3xl font-bold tracking-normal text-neutral sm:text-4xl">{{ $title }}</h1>

            @if ($description)
                <p class="mt-2 max-w-2xl text-sm leading-6 text-base-content/65">{{ $description }}</p>
            @endif
        </div>

        @if ($slot->isNotEmpty())
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                {{ $slot }}
            </div>
        @elseif ($icon)
            <div class="hidden size-12 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15 lg:grid">
                <flux:icon :name="$icon" class="size-6" />
            </div>
        @endif
    </div>
</section>
