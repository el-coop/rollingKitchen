<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTermsOptionsToFiles extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('pdfs', function(Blueprint $table) {
            $table->boolean('terms_and_conditions_nl')->default(false)->after('default_resend_invoice');
            $table->boolean('terms_and_conditions_en')->default(false)->after('terms_and_conditions_nl');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('pdfs', function(Blueprint $table) {
            $table->dropColumn('terms_and_conditions_nl');
            $table->dropColumn('terms_and_conditions_en');

        });
    }
}
