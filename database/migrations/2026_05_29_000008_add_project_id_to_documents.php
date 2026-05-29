<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('quotation_id')->constrained()->nullOnDelete();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });
    }
};
