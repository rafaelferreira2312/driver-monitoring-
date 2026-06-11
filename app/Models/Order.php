<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'Pendente';
    public const STATUS_DELIVERED = 'Entregue';

    protected $fillable = [
        'driver_id',
        'code',
        'delivery_address',
        'status',
        'requested_at',
        'delivered_at',
    ];

    protected $dates = [
        'requested_at',
        'delivered_at',
    ];

    public static function statuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_DELIVERED,
        ];
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
