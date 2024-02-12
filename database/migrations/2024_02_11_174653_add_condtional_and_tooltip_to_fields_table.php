<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('fields', function (Blueprint $table) {
            $table->boolean('has_tooltip')->default(false);
            $table->string('tooltip_en')->nullable();
            $table->string('tooltip_nl')->nullable();
            $table->string('conditional')->nullable();
            $table->string('condition')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('fields', function (Blueprint $table) {
            //
        });
    }
};
