<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('applications', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('kitchen_id')->unsigned();
			$table->integer('number')->nullable();
			$table->string('status');
			$table->year('year');
			$table->json('data');
			$table->integer('socket')->default(0);
			$table->decimal('length', 10, 4);
			$table->decimal('width', 10, 4);
			$table->decimal('terrace_length', 10, 4)->nullable();
			$table->decimal('terrace_width', 10, 4)->nullable();
			$table->integer('seats')->nullable();
			$table->timestamps();
			
			$table->foreign('kitchen_id')
				->references('id')->on('kitchens')
				->onDelete('cascade');
			$table->unique(['kitchen_id', 'year']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('applications');
	}
}
