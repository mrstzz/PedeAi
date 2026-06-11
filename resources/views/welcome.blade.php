<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="pedeai">
    <head>
        @include('partials.head', ['title' => 'Home'])
    </head>

    <body class="app-texture-bg min-h-screen text-base-content antialiased">
        <div class="relative isolate min-h-screen overflow-hidden">
            <header class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex size-11 items-center justify-center rounded-lg border border-base-300 bg-base-100 shadow-sm">
                        <x-application-logo class="size-8" />
                    </span>
                    <span class="text-lg font-bold text-neutral">PedeAi</span>
                </a>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm sm:btn-md">Abrir painel</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-ghost btn-sm sm:btn-md">Entrar</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary btn-sm sm:btn-md">Criar conta</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <main>
                <section class="mx-auto grid min-h-[calc(100vh-5.25rem)] w-full max-w-7xl items-center gap-8 px-4 pb-10 pt-4 sm:px-6 lg:grid-cols-[minmax(0,1fr)_30rem] lg:px-8">
                    <div class="max-w-3xl">
                        <div class="badge badge-secondary badge-outline mb-4">Gestao de atendimento</div>
                        <h1 class="max-w-3xl text-4xl font-bold leading-tight text-neutral sm:text-5xl lg:text-6xl">
                            Controle comandas, mesas e cozinha em um fluxo so.
                        </h1>
                        <p class="mt-5 max-w-2xl text-base leading-7 text-base-content/70 sm:text-lg">
                            O PedeAi organiza a operacao do restaurante desde a abertura da comanda ate o pagamento, com visao rapida para atendimento, cozinha e administracao.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn btn-primary min-h-12">Ir para o dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary min-h-12">Acessar sistema</a>
                            @endauth
                        </div>

                        <dl class="mt-10 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-lg border border-base-300 bg-base-100/85 p-4 shadow-sm">
                                <dt class="text-sm text-base-content/60">Atendimento</dt>
                                <dd class="mt-1 text-2xl font-bold text-primary">Comandas</dd>
                            </div>
                            <div class="rounded-lg border border-base-300 bg-base-100/85 p-4 shadow-sm">
                                <dt class="text-sm text-base-content/60">Cozinha</dt>
                                <dd class="mt-1 text-2xl font-bold text-success">Fila viva</dd>
                            </div>
                            <div class="rounded-lg border border-base-300 bg-base-100/85 p-4 shadow-sm">
                                <dt class="text-sm text-base-content/60">Gestao</dt>
                                <dd class="mt-1 text-2xl font-bold text-secondary">Cardapio</dd>
                            </div>
                        </dl>
                    </div>

                    <aside class="rounded-lg border border-base-300 bg-base-100/95 p-4 shadow-xl">
                        <div class="flex items-center justify-between border-b border-base-300 pb-4">
                            <div>
                                <p class="text-xs font-semibold uppercase text-base-content/50">Agora</p>
                                <h2 class="text-lg font-bold text-neutral">Operacao do salao</h2>
                            </div>
                            <span class="badge badge-success">Online</span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-lg bg-primary/10 p-4">
                                <p class="text-sm text-base-content/60">Abertas</p>
                                <p class="mt-2 text-3xl font-bold text-primary">12</p>
                            </div>
                            <div class="rounded-lg bg-warning/15 p-4">
                                <p class="text-sm text-base-content/60">Na cozinha</p>
                                <p class="mt-2 text-3xl font-bold text-warning">7</p>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div class="rounded-lg border border-base-300 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-semibold text-neutral">Mesa 08</h3>
                                        <p class="text-sm text-base-content/60">3 itens em preparo</p>
                                    </div>
                                    <span class="badge badge-info">Em andamento</span>
                                </div>
                                <progress class="progress progress-info mt-4" value="68" max="100"></progress>
                            </div>

                            <div class="rounded-lg border border-base-300 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-semibold text-neutral">Reserva Ana</h3>
                                        <p class="text-sm text-base-content/60">Mesa 04 as 20:30</p>
                                    </div>
                                    <span class="badge badge-secondary">Confirmada</span>
                                </div>
                            </div>

                            <div class="rounded-lg bg-neutral p-4 text-neutral-content">
                                <p class="text-sm opacity-80">Recebido hoje</p>
                                <p class="mt-1 text-3xl font-bold">R$ 2.840,00</p>
                            </div>
                        </div>
                    </aside>
                </section>
            </main>
        </div>
    </body>
</html>
