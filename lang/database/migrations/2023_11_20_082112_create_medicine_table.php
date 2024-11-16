<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /* 
        Run the migrations.
    */
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('scientific_name');
            $table->string('classification');
            $table->string('company');
            $table->integer('quantity');
            $table->date('expiration_date');
            $table->integer('price');
            $table->timestamps();
        });
    }

    /* 
        Reverse the migrations.
    */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};