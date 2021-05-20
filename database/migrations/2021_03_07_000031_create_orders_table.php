<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->json('raw_source')->nullable();

            $table->text('note')->nullable();
            $table->longText('records')->nullable();
            $table->string('invoice_number')->index()->nullable();
            $table->boolean('tax_invoice_sent')->default(0)->nullable();
            $table->unsignedBigInteger('shipping_fee')->default(0);
            $table->unsignedBigInteger('packing_fee')->default(0);

            $table->unsignedTinyInteger('status')->index();
            $table->unsignedTinyInteger('payment_status')->index();

            $table->foreignId('user_id')->constrained();
            $table->foreignId('lead_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('channel_id')->constrained();
            $table->foreignId('company_id')->constrained();

            $table->foreignId('discount_id')->nullable()->constrained();
            $table->string('discount_error')->nullable();
            $table->bigInteger('total_discount')->default(0);
            $table->bigInteger('total_price')->default(0);

            $table->timestamps();
            $table->softDeletes();

        });
    }
}
