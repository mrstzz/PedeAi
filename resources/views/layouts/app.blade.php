<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="bg-base-200 text-base-content">
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
