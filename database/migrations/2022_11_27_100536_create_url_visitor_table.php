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
        Schema::create('url_visitor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('url_shortcode_id');
            $table->string('ip')->nullable();
            $table->string('os')->nullable();
            $table->string('browser')->nullable();
            $table->string('device')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->foreign('url_shortcode_id')->references("id")->on("url_shortcode")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('url_visitor');
    }
};
