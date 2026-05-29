@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl" class="text-neutral">{{ $title }}</flux:heading>
    <flux:subheading class="text-base-content/65">{{ $description }}</flux:subheading>
</div>
