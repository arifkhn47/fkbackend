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
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->foreignId('recipe_id')->constrained();
            $table->unsignedBigInteger('food_id');
            $table->float('quantity', 10, 2);
            $table->float('calories', 10, 2)->nullable();
            $table->float('protein', 10, 2)->nullable();
            $table->float('carbs', 10, 2)->nullable();
            $table->float('fats', 10, 2)->nullable();

            $table->foreign('food_id')->references('id')->on('foods');
            $table->unique(['recipe_id', 'food_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
