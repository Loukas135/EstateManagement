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
        Schema::create('estates', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            //$table->enum('category', ['Farm', 'Appartment', 'House']);
            $table->unsignedBigInteger('category_id');
            $table->string('city');
            $table->string('street');
            $table->float('latitude');
            $table->float('longitude');
            $table->float('space');
            $table->integer('number_of_rooms');
            $table->integer('bathrooms');
            $table->integer('garages');
            $table->float('price');
            $table->unsignedBigInteger('user_id');
            $table->boolean('active')->default(false);
            $table->boolean('sold')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estates');
    }
};
