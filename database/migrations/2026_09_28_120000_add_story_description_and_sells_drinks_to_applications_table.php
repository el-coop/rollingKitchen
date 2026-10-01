<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('applications', function (Blueprint $table) {
            $table->text('story')->nullable()->after('backstage_width');
            $table->text('description')->nullable()->after('story');
            $table->boolean('sells_drinks')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['story', 'description', 'sells_drinks']);
        });
    }
};
