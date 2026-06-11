<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverMonitoringController extends Controller
{
    public function admin(Request $request)
    {
        $this->ensureRole(User::ROLE_ADMIN);

        $drivers = Driver::query()
            ->withCount([
                'orders as total_orders' => function (Builder $query) use ($request) {
                    $this->applyPeriod($query, $request);
                },
                'orders as delivered_orders' => function (Builder $query) use ($request) {
                    $this->applyPeriod($query, $request);
                    $query->where('status', Order::STATUS_DELIVERED);
                },
            ])
            ->orderBy('name')
            ->get()
            ->map(function (Driver $driver) {
                $driver->classification = $this->classification($driver->total_orders, $driver->delivered_orders);

                return $driver;
            })
            ->filter(function (Driver $driver) use ($request) {
                $filter = $request->get('classification', 'all');

                return $filter === 'all' || $driver->classification['slug'] === $filter;
            })
            ->values();

        return view('dashboard.admin', $this->viewData($request, $drivers));
    }

    public function driver(Request $request)
    {
        $this->ensureRole(User::ROLE_DRIVER);

        $driver = Auth::user()->driver;
        abort_unless($driver, 404);

        $drivers = collect([
            $driver->loadCount([
                'orders as total_orders' => function (Builder $query) use ($request) {
                    $this->applyPeriod($query, $request);
                },
                'orders as delivered_orders' => function (Builder $query) use ($request) {
                    $this->applyPeriod($query, $request);
                    $query->where('status', Order::STATUS_DELIVERED);
                },
            ]),
        ])->map(function (Driver $driver) {
            $driver->classification = $this->classification($driver->total_orders, $driver->delivered_orders);

            return $driver;
        })->filter(function (Driver $driver) use ($request) {
            $filter = $request->get('classification', 'all');

            return $filter === 'all' || $driver->classification['slug'] === $filter;
        })->values();

        return view('dashboard.driver', $this->viewData($request, $drivers));
    }

    public function updateOrder(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $rules = [
            'delivery_address' => ['required', 'string', 'max:255'],
        ];

        if ($request->has('status')) {
            $rules['status'] = ['required', 'in:' . implode(',', Order::statuses())];
        }

        $data = $request->validate($rules);

        $order->delivery_address = $data['delivery_address'];

        if (array_key_exists('status', $data)) {
            $order->status = $data['status'];
            $order->delivered_at = $data['status'] === Order::STATUS_DELIVERED
                ? ($order->delivered_at ?: now())
                : null;
        }

        $order->save();

        return redirect($request->input('redirect_to', route('admin.drivers.index')))
            ->with('success', 'Pedido atualizado com sucesso.');
    }

    private function viewData(Request $request, $drivers)
    {
        $selectedDriver = null;
        $modalOrders = collect();
        $modalType = $request->get('modal');

        if ($request->filled('driver') && in_array($modalType, ['orders', 'deliveries'])) {
            $selectedDriver = Driver::findOrFail($request->get('driver'));

            if (Auth::user()->isDriver()) {
                abort_unless($selectedDriver->user_id === Auth::id(), 403);
            }

            $modalOrders = $selectedDriver->orders()
                ->when($modalType === 'deliveries', function (Builder $query) {
                    $query->where('status', Order::STATUS_DELIVERED);
                })
                ->when($request->filled('start_date'), function (Builder $query) use ($request) {
                    $query->where('requested_at', '>=', $request->get('start_date') . ' 00:00:00');
                })
                ->when($request->filled('end_date'), function (Builder $query) use ($request) {
                    $query->where('requested_at', '<=', $request->get('end_date') . ' 23:59:59');
                })
                ->orderByDesc('requested_at')
                ->get();
        }

        return [
            'drivers' => $drivers,
            'summary' => [
                'drivers' => $drivers->count(),
                'orders' => $drivers->sum('total_orders'),
                'deliveries' => $drivers->sum('delivered_orders'),
            ],
            'statuses' => Order::statuses(),
            'selectedDriver' => $selectedDriver,
            'modalOrders' => $modalOrders,
            'modalType' => $modalType,
            'classification' => $request->get('classification', 'all'),
            'startDate' => $request->get('start_date'),
            'endDate' => $request->get('end_date'),
        ];
    }

    private function applyPeriod(Builder $query, Request $request)
    {
        if ($request->filled('start_date')) {
            $query->where('requested_at', '>=', $request->get('start_date') . ' 00:00:00');
        }

        if ($request->filled('end_date')) {
            $query->where('requested_at', '<=', $request->get('end_date') . ' 23:59:59');
        }
    }

    private function classification($totalOrders, $deliveredOrders)
    {
        if ($totalOrders > 0 && $deliveredOrders === $totalOrders) {
            return ['slug' => 'completed', 'label' => 'Concluído', 'class' => 'is-completed'];
        }

        if ($totalOrders > 0 && $deliveredOrders > ($totalOrders / 2)) {
            return ['slug' => 'almost', 'label' => 'Próximo de terminar', 'class' => 'is-almost'];
        }

        return ['slug' => 'alert', 'label' => 'Em alerta', 'class' => 'is-alert'];
    }

    private function ensureRole($role)
    {
        abort_unless(Auth::check() && Auth::user()->role === $role, 403);
    }

    private function authorizeOrder(Order $order)
    {
        abort_unless(Auth::check(), 403);

        if (Auth::user()->isDriver()) {
            abort_unless($order->driver && $order->driver->user_id === Auth::id(), 403);
        }
    }
}
