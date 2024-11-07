<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create("orders_cod", function (Blueprint $table) {
            $table->autoIncrement("id");
            $table->unsignedBigInteger("order_id")->comment("Order ID");
            // add create and update datetime
            $table->timestamps();
            //$table->foreign("order_id")->references("id")->on("orders");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists("orders_cod");
    }
};
