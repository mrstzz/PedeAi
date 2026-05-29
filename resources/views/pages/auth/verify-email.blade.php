<x-layouts::auth :title="__('Verificacao de email')">
    <div class="mt-4 flex flex-col gap-6">
        <flux:text class="text-center">
            {{ __('Verifique seu endereco de email clicando no link que acabamos de enviar.') }}
        </flux:text>

        @if (session('status') == 'verification-link-sent')
            <flux:text class="text-center font-medium !dark:text-green-400 !text-green-600">
                {{ __('Um novo link de verificacao foi enviado para o email informado no cadastro.') }}
            </flux:text>
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <flux:button type="submit" variant="primary" class="w-full !bg-secondary !text-secondary-content hover:!bg-secondary/90">
                    {{ __('Reenviar email de verificacao') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button variant="ghost" type="submit" class="text-sm cursor-pointer" data-test="logout-button">
                    {{ __('Sair') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts::auth>
