<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetListFilesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('set_list_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('band_id')->unsigned();
            $table->string('file');
            $table->string('owned');
            $table->string('protected');

            $table->timestamps();

            $table->foreign('band_id')
                ->references('id')->on('bands')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('set_list_files');
    }
}
