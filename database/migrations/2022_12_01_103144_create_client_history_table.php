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
        Schema::create('client_history', function (Blueprint $table) {
            $table->id();
            $table->string('ip')->nullable();
            $table->integer('status')->nullable();
            $table->string('method')->nullable();
            $table->text('uri')->nullable();
            $table->longText('body')->nullable();
            $table->longText('header')->nullable();
            $table->longText('response')->nullable();
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
        Schema::dropIfExists('client_history');
    }
};
