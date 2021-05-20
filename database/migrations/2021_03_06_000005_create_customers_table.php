<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name')->index();
            $table->string('last_name')->nullable()->index();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique();
            $table->date('date_of_birth')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('default_address_id')->nullable();
            $table->tinyInteger('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
