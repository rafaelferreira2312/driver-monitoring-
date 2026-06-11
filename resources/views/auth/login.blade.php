@extends('layouts.app')

@section('title', $title . ' | Monitoramento')

@section('content')
    <main class="auth-page">
        <section class="auth-card">
            <img class="logo" src="{{ asset('images/logmanager-logo.svg') }}" alt="LogManager">

            <span class="eyebrow">{{ $type === 'admin' ? 'Administrador' : 'Motorista' }}</span>
            <h1>{{ $title }}</h1>
            <p class="muted">{{ $subtitle }}</p>

            @if ($errors->any())
                <div class="alert">{{ $errors->first() }}</div>
            @endif

            <form class="form" method="POST" action="{{ route('login.authenticate', $type) }}">
                @csrf

                <label>
                    E-mail
                    <input type="email" name="email" value="{{ old('email', $demoEmail) }}" required autofocus>
                </label>

                <label>
                    Senha
                    <input type="password" name="password" value="123456" required>
                </label>

                <label>
                    <span>
                        <input type="checkbox" name="remember" value="1">
                        Manter conectado
                    </span>
                </label>

                <button class="btn btn-primary" type="submit">Entrar</button>
            </form>

            <div class="auth-links">
                <a class="btn btn-light" href="{{ route('login.admin') }}">Login administrador</a>
                <a class="btn btn-light" href="{{ route('login.driver') }}">Login motorista</a>
            </div>

            <div class="demo-box">
                <strong>Usuário de teste:</strong><br>
                {{ $demoEmail }}<br>
                Senha: 123456
            </div>
        </section>

        <section class="auth-panel {{ $type === 'driver' ? 'driver' : '' }}">
            <span class="eyebrow" style="color: rgba(255,255,255,.72)">Same day delivery</span>
            <h1>{{ $type === 'admin' ? 'Operação sob controle.' : 'Sua rota em foco.' }}</h1>
            <p>
                {{ $type === 'admin'
                    ? 'Acompanhe pedidos, entregas e gargalos por motorista em uma única tela.'
                    : 'Consulte seus pedidos, acompanhe entregas concluidas e mantenha endereços atualizados.' }}
            </p>
        </section>
    </main>
@endsection
