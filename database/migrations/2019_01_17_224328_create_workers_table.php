<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkersTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('workers', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->tinyInteger('type');
			$table->boolean('supervisor');
			$table->boolean('liability')->default(false);
			$table->boolean('submitted')->default(false);
			$table->boolean('approved')->default(false);
			$table->json('data');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('workers');
	}
}
