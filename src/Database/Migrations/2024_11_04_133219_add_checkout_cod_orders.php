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
        Schema::create("order_cods", function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('order_id')->unsigned()->nullable()->comment("Order ID");
            // add create and update datetime
            $table->string("ip_address")->nullable()->comment("IP Address");
            $table->string("ip_country")->nullable()->comment("IP Country");
            $table->timestamps();
            //$table->foreign("order_id")->references("id")->on("orders");
            //$table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::table("order_cods", function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists("order_cods");
    }
};
