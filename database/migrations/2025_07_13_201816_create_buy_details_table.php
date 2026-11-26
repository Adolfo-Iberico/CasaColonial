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
        Schema::create('buy_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('buy_id');
            $table->foreign('buy_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('name');
            $table->integer('cantidad');
            $table->decimal('precio_unitario');
            $table->decimal('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_details');
    }
};
