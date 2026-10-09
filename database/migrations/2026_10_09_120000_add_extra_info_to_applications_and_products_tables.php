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
            $table->boolean('outside_serving')->nullable()->after('sells_drinks');
            $table->decimal('outside_serving_depth', 8, 2)->nullable()->after('outside_serving');
            $table->decimal('path_distance', 8, 2)->nullable()->after('outside_serving_depth');
            $table->boolean('own_terrace_furniture')->nullable()->after('path_distance');
            $table->boolean('needs_backstage')->nullable()->after('own_terrace_furniture');
            $table->boolean('cooks_in_backstage')->nullable()->after('needs_backstage');
            $table->boolean('open_flame')->nullable()->after('cooks_in_backstage');
            $table->boolean('uses_gas')->nullable()->after('open_flame');
            $table->boolean('uses_electric_devices')->nullable()->after('uses_gas');
            $table->boolean('deep_fryer')->nullable()->after('uses_electric_devices');
            $table->boolean('religious_dishes')->nullable()->after('deep_fryer');
            $table->boolean('vegan_dishes')->nullable()->after('religious_dishes');
            $table->text('other_details')->nullable()->after('vegan_dishes');
            $table->timestamp('extra_info_submitted_at')->nullable()->after('other_details');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('vegan')->default(false)->after('category');
            $table->text('religious_note')->nullable()->after('vegan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'outside_serving',
                'outside_serving_depth',
                'path_distance',
                'own_terrace_furniture',
                'needs_backstage',
                'cooks_in_backstage',
                'open_flame',
                'uses_gas',
                'uses_electric_devices',
                'deep_fryer',
                'religious_dishes',
                'vegan_dishes',
                'other_details',
                'extra_info_submitted_at',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['vegan', 'religious_note']);
        });
    }
};
