<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'company_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });

            DB::table('products')
                ->join('companies', 'companies.user_id', '=', 'products.user_id')
                ->whereNull('products.company_id')
                ->update(['products.company_id' => DB::raw('companies.id')]);

            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'company_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropConstrainedForeignId('company_id');
            });
        }
    }
};
