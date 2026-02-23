<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_spotters', function (Blueprint $table) {
            $table->tinyInteger('is_premium')->default(0)->after('pdf_file')->comment('0: free, 1: premium');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_spotters', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });
    }
};
