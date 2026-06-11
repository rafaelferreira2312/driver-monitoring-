<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DriverMonitoringSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@logmanager.test'],
            [
                'name' => 'Camila Administradora',
                'password' => Hash::make('123456'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        $drivers = [
            [
                'name' => 'Joao Pereira',
                'email' => 'joao.motorista@logmanager.test',
                'document' => '111.222.333-44',
                'phone' => '(44) 99999-1111',
                'orders' => [
                    ['code' => 'LM-1001', 'address' => 'Av. Brasil, 1200 - Maringa/PR', 'status' => Order::STATUS_DELIVERED, 'requested' => '-4 days', 'delivered' => '-4 days 2 hours'],
                    ['code' => 'LM-1002', 'address' => 'Rua Santos Dumont, 40 - Maringa/PR', 'status' => Order::STATUS_DELIVERED, 'requested' => '-3 days', 'delivered' => '-3 days 1 hour'],
                    ['code' => 'LM-1003', 'address' => 'Rua Nilo Cairo, 88 - Maringa/PR', 'status' => Order::STATUS_DELIVERED, 'requested' => '-2 days', 'delivered' => '-2 days 3 hours'],
                ],
            ],
            [
                'name' => 'Maria Oliveira',
                'email' => 'maria.motorista@logmanager.test',
                'document' => '222.333.444-55',
                'phone' => '(44) 99999-2222',
                'orders' => [
                    ['code' => 'LM-2001', 'address' => 'Rua Neo Alves Martins, 500 - Maringa/PR', 'status' => Order::STATUS_DELIVERED, 'requested' => '-5 days', 'delivered' => '-5 days 4 hours'],
                    ['code' => 'LM-2002', 'address' => 'Av. Cerro Azul, 980 - Maringa/PR', 'status' => Order::STATUS_DELIVERED, 'requested' => '-2 days', 'delivered' => '-2 days 2 hours'],
                    ['code' => 'LM-2003', 'address' => 'Rua Joubert de Carvalho, 212 - Maringa/PR', 'status' => Order::STATUS_PENDING, 'requested' => '-1 day', 'delivered' => null],
                ],
            ],
            [
                'name' => 'Carlos Souza',
                'email' => 'carlos.motorista@logmanager.test',
                'document' => '333.444.555-66',
                'phone' => '(44) 99999-3333',
                'orders' => [
                    ['code' => 'LM-3001', 'address' => 'Av. Colombo, 3500 - Maringa/PR', 'status' => Order::STATUS_DELIVERED, 'requested' => '-6 days', 'delivered' => '-6 days 5 hours'],
                    ['code' => 'LM-3002', 'address' => 'Rua Pioneiro Joao Nunes, 74 - Maringa/PR', 'status' => Order::STATUS_PENDING, 'requested' => '-3 days', 'delivered' => null],
                    ['code' => 'LM-3003', 'address' => 'Rua Curitiba, 45 - Sarandi/PR', 'status' => Order::STATUS_PENDING, 'requested' => '-2 days', 'delivered' => null],
                    ['code' => 'LM-3004', 'address' => 'Av. Londrina, 812 - Maringa/PR', 'status' => Order::STATUS_PENDING, 'requested' => '-1 day', 'delivered' => null],
                ],
            ],
        ];

        foreach ($drivers as $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'password' => Hash::make('123456'),
                    'role' => User::ROLE_DRIVER,
                ]
            );

            $driver = Driver::updateOrCreate(
                ['document' => $item['document']],
                [
                    'user_id' => $user->id,
                    'name' => $item['name'],
                    'phone' => $item['phone'],
                ]
            );

            foreach ($item['orders'] as $order) {
                Order::updateOrCreate(
                    ['code' => $order['code']],
                    [
                        'driver_id' => $driver->id,
                        'delivery_address' => $order['address'],
                        'status' => $order['status'],
                        'requested_at' => Carbon::parse($order['requested']),
                        'delivered_at' => $order['delivered'] ? Carbon::parse($order['delivered']) : null,
                    ]
                );
            }
        }
    }
}
