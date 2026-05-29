
@props([
    'title' => null,
    'description' => null,
    'bodyClass' => 'py-',
])

<article {{ $attributes->class(['card rounded-lg border border-base-300 bg-base-100 shadow-sm']) }}>
    <div @class(['card-body gap-4', $bodyClass])>
        @if ($title || $description)
            <header>
                @if ($title)
                    <h2 class="card-title text-base text-base-content/70">{{ $title }}</h2>
                @endif

                @if ($description)
                    <p class="text-sm text-base-content/50">{{ $description }}</p>
                @endif
            </header>
        @endif

        {{ $slot }}
    </div>
</article>

