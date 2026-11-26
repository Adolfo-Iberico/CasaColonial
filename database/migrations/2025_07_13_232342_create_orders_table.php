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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id'); // Clave foránea para vincular con clientes 📚
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
            $table->unsignedBigInteger('table_id'); // Clave foránea para vincular con mesas 📚
            $table->foreign('table_id')
                ->references('id')
                ->on('tables')
                ->onDelete('cascade');
            $table->string('nombre_plato'); 
            $table->decimal('total', 10, 2); 
            $table->string('metodo_pago'); // Método de pago 💳
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
