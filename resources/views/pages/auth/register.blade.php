<x-layouts::auth :title="__('Cadastro')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Crie sua conta')" :description="__('Preencha os dados abaixo para entrar no PedeAi')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Nome')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Nome completo')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="Insira o seu email..."
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Senha')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Digite sua senha..."
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirmar senha')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Confirme sua senha..."
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full !bg-secondary !text-secondary-content hover:!bg-secondary/90" data-test="register-user-button">
                    {{ __('Criar conta') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-center text-sm text-base-content/65 rtl:space-x-reverse">
            <span>{{ __('Ja tem uma conta?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Entrar') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
