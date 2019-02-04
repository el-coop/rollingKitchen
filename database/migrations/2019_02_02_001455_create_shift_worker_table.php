<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftWorkerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_worker', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('worker_id')->unsigned();
			$table->integer('shift_id')->unsigned();
			$table->integer('work_function_id')->unsigned();
			$table->time('start_time');
			$table->time('end_time');
			$table->timestamps();
			$table->foreign('worker_id')
				->references('id')->on('workers')
				->onDelete('cascade');
			$table->foreign('shift_id')
				->references('id')->on('shifts')
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
        Schema::dropIfExists('shift_worker');
    }
}
