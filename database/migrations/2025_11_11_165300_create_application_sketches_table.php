<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('application_sketches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('application_id')->unsigned();
            $table->string('file');
            $table->timestamps();

            $table->foreign('application_id')
                ->references('id')->on('applications')
                ->onDelete('cascade');        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_sketches');
    }
};
