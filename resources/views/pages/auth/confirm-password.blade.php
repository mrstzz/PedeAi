<x-layouts::auth :title="__('Confirmar senha')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Confirmar senha')"
            :description="__('Esta é uma área segura. Confirme sua senha antes de continuar.')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify
            options-route="passkey.confirm-options"
            submit-route="passkey.confirm"
            :label="__('Confirmar com chave de acesso')"
            :loading-label="__('Confirmando...')"
            :separator="__('Ou confirme com senha')"
        />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="password"
                :label="__('Senha')"
                type="password"
                required
                autocomplete="current-password"
                placeholder="Digite sua senha..."
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full !bg-secondary !text-secondary-content hover:!bg-secondary/90" data-test="confirm-password-button">
                {{ __('Confirmar') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
