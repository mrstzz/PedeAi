<div class="grid gap-6 lg:grid-cols-[15rem_minmax(0,1fr)]">
    <div class="w-full">
        <div class="ps-0 sm:ps-0">
            <div class="rounded-lg border border-base-300/80 bg-base-100 p-3 shadow-sm">
                <flux:navlist aria-label="{{ __('Configurações') }}">
                    <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Perfil') }}</flux:navlist.item>
                    <flux:navlist.item :href="route('security.edit')" wire:navigate>{{ __('Segurança') }}</flux:navlist.item>
                    <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Aparência') }}</flux:navlist.item>
                </flux:navlist>
            </div>
        </div>
    </div>

    <div class="min-w-0 rounded-lg border border-base-300/80 bg-base-100 p-5 shadow-sm sm:p-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-2xl">
            {{ $slot }}
        </div>
    </div>
</div>