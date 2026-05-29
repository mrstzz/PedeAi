<x-layouts::auth :title="__('Redefinir senha')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Redefinir senha')" :description="__('Digite sua nova senha abaixo')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <flux:input
                name="email"
                value="{{ request('email') }}"
                :label="__('Email')"
                type="email"
                required
                autocomplete="email"
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
                <flux:button type="submit" variant="primary" class="w-full !bg-secondary !text-secondary-content hover:!bg-secondary/90" data-test="reset-password-button">
                    {{ __('Redefinir senha') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts::auth>
