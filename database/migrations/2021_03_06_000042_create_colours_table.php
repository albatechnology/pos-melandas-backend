<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColoursTable extends Migration
{
    public function up()
    {
        Schema::create('colours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description');
            $table->foreignId('company_id')->constrained();
            $table->foreignId('product_brand_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
