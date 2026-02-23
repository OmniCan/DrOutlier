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
        Schema::create('new_spotters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category')->comment('This refers to chapter from new_spotters_categories table');
            $table->string('title');
            $table->integer('sort_order')->default(0);
            $table->string('image')->nullable();
            $table->text('description')->nullable()->comment('Optional description field');
            $table->string('pdf_file')->nullable()->comment('PDF file upload');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_spotters');
    }
};
