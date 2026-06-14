@props([
    'tone' => 'info',
    'title' => null,
    'icon' => null,
])

@php
    $tones = [
        'info' => 'border-info/20 bg-info/10 text-info',
        'success' => 'border-success/20 bg-success/10 text-success',
        'warning' => 'border-warning/30 bg-warning/10 text-warning',
        'error' => 'border-error/20 bg-error/10 text-error',
    ];

    $icons = [
        'info' => 'information-circle',
        'success' => 'check-circle',
        'warning' => 'exclamation-triangle',
        'error' => 'exclamation-triangle',
    ];
@endphp

<div {{ $attributes->class(['alert rounded-lg border', $tones[$tone] ?? $tones['info']]) }}>
    <flux:icon :name="$icon ?? ($icons[$tone] ?? 'information-circle')" class="size-5 shrink-0" />
    <div>
        @if ($title)
            <h2 class="font-semibold">{{ $title }}</h2>
        @endif
        <div class="text-sm">{{ $slot }}</div>
    </div>
</div>
