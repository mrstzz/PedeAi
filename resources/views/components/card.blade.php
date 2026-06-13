
@props([
    'title' => null,
    'description' => null,
    'bodyClass' => 'p-5 sm:p-6',
])

<article {{ $attributes->class(['card rounded-lg border border-base-300/80 bg-base-100 shadow-sm']) }}>
    <div @class(['card-body gap-4', $bodyClass])>
        @if ($title || $description)
            <header>
                @if ($title)
                    <h2 class="card-title text-base font-semibold text-neutral">{{ $title }}</h2>
                @endif

                @if ($description)
                    <p class="text-sm text-base-content/55">{{ $description }}</p>
                @endif
            </header>
        @endif

        {{ $slot }}
    </div>
</article>
