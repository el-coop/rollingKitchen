<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidColumnToWorkersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('workers', function (Blueprint $table) {
            $table->boolean('paid')->after('approved');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn('paid');
        });
    }
}
