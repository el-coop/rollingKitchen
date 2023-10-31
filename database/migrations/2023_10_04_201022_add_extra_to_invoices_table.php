<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('extra_name')->nullable();
            $table->decimal('extra_amount', 10, 2)->default(0);
            $table->text('note')->nullable();
            $table->string('number_datatable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('extra_name');
            $table->dropColumn('extra_amount');
            $table->dropColumn('note');
            $table->dropColumn('number_datatable');

        });
    }
};
