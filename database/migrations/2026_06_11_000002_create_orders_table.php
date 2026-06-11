<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('delivery_address');
            $table->enum('status', Order::statuses())->default(Order::STATUS_PENDING);
            $table->dateTime('requested_at');
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'requested_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
