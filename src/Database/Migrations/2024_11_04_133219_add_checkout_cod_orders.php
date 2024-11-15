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
            $table->bigIncrements("id");
            $table->unsignedBigInteger("order_id")->comment("Order ID");
            // add create and update datetime
            $table->string("ip_address")->nullable()->comment("IP Address");
            $table->string("ip_country")->nullable()->comment("IP Country");
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
        Schema::dropIfExists("order_cods");
    }
};
