<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('station');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['active', 'station']);
        });

        Schema::create('cash_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('opening_amount', 10, 2);
            $table->decimal('expected_cash', 10, 2)->nullable();
            $table->decimal('counted_cash', 10, 2)->nullable();
            $table->decimal('difference', 10, 2)->nullable();
            $table->string('status')->default('open');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->index(['opened_by', 'status']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->unsignedBigInteger('table_id')->nullable()->change();
            $table->foreignId('created_by')->nullable()->after('table_id')->constrained('users')->nullOnDelete();
            $table->string('service_type')->default('dine_in');
            $table->string('status')->default('pending')->index();
            $table->string('document_type')->nullable();
            $table->string('customer_document', 32)->nullable();
        });

        DB::table('orders')->update(['status' => 'completed']);

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('station');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->string('status')->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamps();
            $table->index(['station', 'status']);
        });

        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cash_session_id')->constrained()->restrictOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('method');
            $table->decimal('amount', 10, 2);
            $table->timestamp('paid_at');
            $table->timestamps();
            $table->index(['cash_session_id', 'method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
        Schema::dropIfExists('order_items');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['created_by', 'service_type', 'status', 'document_type', 'customer_document']);
        });

        Schema::dropIfExists('cash_sessions');
        Schema::dropIfExists('products');
    }
};
