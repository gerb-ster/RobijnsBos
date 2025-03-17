<?php

namespace robijnsbos_dt_app\database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('species', function (Blueprint $table) {
      // identifiers
      $table->id();
      $table->string('uuid', 36)->unique();
      $table->string('number', 25)->unique();

      // data
      $table->string('dutch_name', 512);
      $table->string('latin_name', 512);

      $table->foreignId('latin_family_id')->constrained('latin_families');

      $table->json('blossom_month')->nullable();
      $table->string('height', 32)->nullable();

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('species');
  }
};
