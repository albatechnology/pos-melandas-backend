<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('amount');
            $table->string('reference')->nullable();
            $table->string('status');
            $table->string('reason')->nullable();
            $table->foreignId('payment_type_id')->constrained();
            $table->foreignId('approved_by_id')->nullable()->constrained('users');
            $table->foreignId('added_by_id')->nullable()->constrained('users');
            $table->foreignId('order_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
