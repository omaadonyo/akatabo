<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->foreignId('fabric_roll_id')->nullable()->after('product_id')->constrained('fabric_rolls')->nullOnDelete();
        });
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('fabric_roll_id')->nullable()->after('product_id')->constrained('fabric_rolls')->nullOnDelete();
        });
        Schema::table('receipt_items', function (Blueprint $table) {
            $table->foreignId('fabric_roll_id')->nullable()->after('product_id')->constrained('fabric_rolls')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotation_items', fn (Blueprint $table) => $table->dropColumn('fabric_roll_id'));
        Schema::table('invoice_items', fn (Blueprint $table) => $table->dropColumn('fabric_roll_id'));
        Schema::table('receipt_items', fn (Blueprint $table) => $table->dropColumn('fabric_roll_id'));
    }
};
