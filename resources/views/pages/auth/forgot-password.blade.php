<x-layouts::auth :title="__('Recuperar senha')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Recuperar senha')" :description="__('Informe seu e-mail para receber o link de redefinição')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <!-- E-mail Address -->
            <flux:input
                name="email"
                :label="__('E-mail')"
                type="email"
                required
                autofocus
                placeholder="Insira o seu e-mail..."
            />

            <flux:button variant="primary" type="submit" class="w-full !bg-secondary !text-secondary-content hover:!bg-secondary/90" data-test="email-password-reset-link-button">
                {{ __('Enviar link de recuperação') }}
            </flux:button>
        </form>

        <div class="space-x-1 text-center text-sm text-base-content/65 rtl:space-x-reverse">
            <span>{{ __('Ou volte para') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('entrar') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
