<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sistema de Vendas Laravel</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    </head>
    <body>
        <header class="header">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Registrar</a>
                    @endif
                @endauth
            @endif
        </header>

        <main class="main">
            <div class="content">
                <h1 class="title">SEJA BEM VINDO AO<br>SISTEMA DE VENDAS LARAVEL</h1>
                <p class="subtitle">Gerencie suas vendas de forma simples e eficiente</p>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn">Ir para o Dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="btn">Começar Agora</a>
                @endauth
            </div>
        </main>
    </body>
</html>
