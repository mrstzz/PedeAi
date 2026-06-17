@if (auth()->user()?->isWaiter())
    <x-layouts::app.waiter :title="$title ?? null">
        {{ $slot }}
    </x-layouts::app.waiter>
@else
    <x-layouts::app.sidebar :title="$title ?? null">
        <flux:main class="app-texture-bg text-base-content max-md:flex max-md:flex-col max-md:items-center">
            <div class="w-full max-w-full flex flex-col max-md:items-center md:block">
                {{ $slot }}
            </div>
        </flux:main>
    </x-layouts::app.sidebar>
@endif