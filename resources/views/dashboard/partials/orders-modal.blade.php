@if ($selectedDriver)
    <div class="modal-backdrop">
        <section class="modal {{ $modalType === 'deliveries' ? 'modal-deliveries' : '' }}">
            <div class="modal-header">
                <div>
                    <p class="muted">{{ $modalType === 'deliveries' ? 'Detalhamento de entregas' : 'Detalhamento de pedidos' }}</p>
                    <h2>{{ $selectedDriver->name }}</h2>
                </div>
                <a class="btn btn-light" href="{{ $closeRoute }}">Fechar</a>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Código do pedido</th>
                            <th>Endereço de entrega</th>
                            @if ($modalType === 'deliveries')
                                <th>Data e horrio da entrega</th>
                            @else
                                <th>Status</th>
                            @endif
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($modalOrders as $order)
                            <tr>
                                <td><strong>{{ $order->code }}</strong></td>
                                <td colspan="3">
                                    <form class="inline-form" method="POST" action="{{ route('orders.update', $order) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">

                                        <label>
                                            Endereco
                                            <input type="text" name="delivery_address" value="{{ old('delivery_address', $order->delivery_address) }}" required>
                                        </label>

                                        @if ($modalType === 'deliveries')
                                            <div>
                                                <strong>{{ optional($order->delivered_at)->format('d/m/Y H:i') ?: 'Não informado' }}</strong>
                                            </div>
                                        @else
                                            <label>
                                                Status
                                                <select name="status" required>
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                                            {{ $status }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </label>
                                        @endif

                                        <button class="btn btn-primary" type="submit">Salvar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="empty" colspan="4">Nenhum pedido encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endif
