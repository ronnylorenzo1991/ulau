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
        Schema::create('navs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon', 40);
            $table->string('code', 40);
            $table->string('route_name')->nullable();
            $table->string('class')->nullable();
            $table->boolean('parent')->default(0);
            $table->unsignedBigInteger('nav_id')->nullable();
            $table->timestamps();
            $table->foreign('nav_id')->references('id')->on('navs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navs');
    }
};
