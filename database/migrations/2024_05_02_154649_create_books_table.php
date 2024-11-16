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
       
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('book_name');
           // $table->string('title');
            $table->string('cover_image')->nullable();
            $table->text('abstract')->nullable();
            $table->string('pdf')->nullable();
         //   $table->foreignId('author_id')->constrained('authors')->onDelete('cascade');
         //   $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');

            $table->unsignedBigInteger('author_id'); 
            $table->unsignedBigInteger('category_id');
            $table->timestamps();
        });
    
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
