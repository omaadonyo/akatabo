<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->index('status');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('status');
        });
        Schema::table('receipts', function (Blueprint $table) {
            $table->index('status');
        });
        Schema::table('fabric_rolls', function (Blueprint $table) {
            $table->index('status');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->index('status');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('status');
            $table->index('type');
        });
        Schema::table('customer_deposits', function (Blueprint $table) {
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        Schema::table('fabric_rolls', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['type']);
        });
        Schema::table('customer_deposits', function (Blueprint $table) {
            $table->dropIndex(['type']);
        });
    }
};
