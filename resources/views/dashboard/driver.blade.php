@extends('layouts.app')

@section('title', 'Motorista | Monitoramento')

@section('content')
    <main class="page">
        <header class="topbar">
            <div class="brand">
                <img src="{{ asset('images/logmanager-logo.svg') }}" alt="LogManager">
                <div>
                    <p class="muted">Área do motorista</p>
                    <h2>Meus Pedidos e Entregas</h2>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-danger" type="submit">Sair</button>
            </form>
        </header>

        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <section class="summary">
            <article class="card">
                <span class="muted">Meu cadastro</span>
                <strong>{{ $summary['drivers'] }}</strong>
            </article>
            <article class="card">
                <span class="muted">Meus pedidos</span>
                <strong>{{ $summary['orders'] }}</strong>
            </article>
            <article class="card">
                <span class="muted">Entregas concluidas</span>
                <strong>{{ $summary['deliveries'] }}</strong>
            </article>
        </section>

        <form class="card filters" method="GET" action="{{ route('driver.orders.index') }}">
            <label>
                Inicio do periodo
                <input type="date" name="start_date" value="{{ $startDate }}">
            </label>
            <label>
                Fim do periodo
                <input type="date" name="end_date" value="{{ $endDate }}">
            </label>
            <label>
                Status visual
                <select name="classification">
                    <option value="all" {{ $classification === 'all' ? 'selected' : '' }}>Todos</option>
                    <option value="completed" {{ $classification === 'completed' ? 'selected' : '' }}>Concluido</option>
                    <option value="almost" {{ $classification === 'almost' ? 'selected' : '' }}>Próximo de terminar</option>
                    <option value="alert" {{ $classification === 'alert' ? 'selected' : '' }}>Em alerta</option>
                </select>
            </label>
            <button class="btn btn-primary" type="submit">Filtrar</button>
        </form>

        <section class="card table-card">
            <table>
                <thead>
                    <tr>
                        <th>Motorista</th>
                        <th>Total de pedidos</th>
                        <th>Pedidos entregues</th>
                        <th>Classificação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($drivers as $driver)
                        <tr>
                            <td>
                                <strong>{{ $driver->name }}</strong>
                                <p class="muted">{{ $driver->phone ?: 'Telefone não informado' }}</p>
                            </td>
                            <td>
                                <a class="metric" href="{{ request()->fullUrlWithQuery(['driver' => $driver->id, 'modal' => 'orders']) }}">
                                    {{ $driver->total_orders }}
                                </a>
                            </td>
                            <td>
                                <a class="metric" href="{{ request()->fullUrlWithQuery(['driver' => $driver->id, 'modal' => 'deliveries']) }}">
                                    {{ $driver->delivered_orders }}
                                </a>
                            </td>
                            <td>
                                <span class="pill {{ $driver->classification['class'] }}">
                                    {{ $driver->classification['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="4">Nenhum pedido encontrado para os filtros selecionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>

    @include('dashboard.partials.orders-modal', ['closeRoute' => route('driver.orders.index', request()->except(['driver', 'modal']))])
@endsection
