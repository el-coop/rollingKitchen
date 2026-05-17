<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevenueToInvoicesTable extends Migration {
    public function up() {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('revenue', 12, 2)->nullable()->after('extra_amount');
        });
    }

    public function down() {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('revenue');
        });
    }
}
