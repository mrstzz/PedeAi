<x-layouts::auth :title="__('Entrar')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Entre para o nosso time')" :description="__('Acesse sua conta para gerenciar os pedidos')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- E-mail Address -->
            <flux:input
                name="email"
                :label="__('E-mail')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="Insira o seu e-mail..."
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Senha')"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Digite sua senha..."
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Esqueceu sua senha?') }}
                    </flux:link>
                @endif
            </div>

            <flux:checkbox name="remember" :label="__('Lembrar de mim')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full !bg-secondary !text-secondary-content hover:!bg-secondary/90" data-test="login-button">
                    {{ __('Entrar') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-center text-sm text-base-content/65 rtl:space-x-reverse">
            <span>{{ __('Ainda não tem uma conta?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Criar conta') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
