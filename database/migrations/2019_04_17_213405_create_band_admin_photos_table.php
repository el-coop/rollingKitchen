<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBandAdminPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('band_admin_photos', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('band_admin_id')->unsigned();
			$table->string('file');
			$table->timestamps();

			$table->foreign('band_admin_id')
				->references('id')->on('band_admins')
				->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('band_admin_photos');
    }
}
