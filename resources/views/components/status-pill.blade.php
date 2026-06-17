@props([
    'label',
    'tone' => 'neutral',
])

@php
    $tones = [
        'primary' => 'bg-primary/10 text-primary ring-primary/20',
        'secondary' => 'bg-secondary/10 text-secondary ring-secondary/20',
        'info' => 'bg-info/10 text-info ring-info/20',
        'success' => 'bg-success/10 text-success ring-success/20',
        'warning' => 'bg-warning/15 text-warning ring-warning/30',
        'error' => 'bg-error/10 text-error ring-error/20',
        'neutral' => 'bg-base-200 text-base-content/70 ring-base-300',
    ];

    $dots = [
        'primary' => 'bg-primary',
        'secondary' => 'bg-secondary',
        'info' => 'bg-info',
        'success' => 'bg-success',
        'warning' => 'bg-warning',
        'error' => 'bg-error',
        'neutral' => 'bg-base-content/35',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1', $tones[$tone] ?? $tones['neutral']]) }}>
    <span class="size-1.5 rounded-full {{ $dots[$tone] ?? $dots['neutral'] }}"></span>
    {{ $label }}
</span>
