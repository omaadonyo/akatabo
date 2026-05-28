<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropUnique('quotations_number_unique');
            $table->unique(['company_id', 'number'], 'quotations_company_number_unique');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique('invoices_number_unique');
            $table->unique(['company_id', 'number'], 'invoices_company_number_unique');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropUnique('receipts_number_unique');
            $table->unique(['company_id', 'number'], 'receipts_company_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropUnique('quotations_company_number_unique');
            $table->unique('number', 'quotations_number_unique');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique('invoices_company_number_unique');
            $table->unique('number', 'invoices_number_unique');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropUnique('receipts_company_number_unique');
            $table->unique('number', 'receipts_number_unique');
        });
    }
};
