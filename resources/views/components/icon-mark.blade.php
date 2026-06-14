@props([
    'icon',
    'accent' => 'text-primary',
])

@php
    $tone = collect(explode(' ', $accent))
        ->first(fn ($class) => str_starts_with($class, 'text-')) ?? 'text-primary';

    $classes = $attributes->class(["shrink-0 {$tone}"])->get('class');
@endphp

@switch($icon)
    @case('banknotes')
        <flux:icon.banknotes class="{{ $classes }}" />
        @break

    @case('book-open')
        <flux:icon.book-open class="{{ $classes }}" />
        @break

    @case('calendar-days')
        <flux:icon.calendar-days class="{{ $classes }}" />
        @break

    @case('check-circle')
        <flux:icon.check-circle class="{{ $classes }}" />
        @break

    @case('clipboard-document-list')
        <flux:icon.clipboard-document-list class="{{ $classes }}" />
        @break

    @case('clock')
        <flux:icon.clock class="{{ $classes }}" />
        @break

    @case('exclamation-triangle')
        <flux:icon.exclamation-triangle class="{{ $classes }}" />
        @break

    @case('key')
        <flux:icon.key class="{{ $classes }}" />
        @break

    @case('pencil-square')
        <flux:icon.pencil-square class="{{ $classes }}" />
        @break

    @case('queue-list')
        <flux:icon.queue-list class="{{ $classes }}" />
        @break

    @case('shield-check')
        <flux:icon.shield-check class="{{ $classes }}" />
        @break

    @case('table-cells')
        <flux:icon.table-cells class="{{ $classes }}" />
        @break

    @case('ticket')
        <flux:icon.ticket class="{{ $classes }}" />
        @break

    @case('users')
        <flux:icon.users class="{{ $classes }}" />
        @break

    @case('wrench-screwdriver')
        <flux:icon.wrench-screwdriver class="{{ $classes }}" />
        @break

    @case('x-circle')
        <flux:icon.x-circle class="{{ $classes }}" />
        @break

    @default
        <flux:icon :name="$icon" class="{{ $classes }}" />
@endswitch
