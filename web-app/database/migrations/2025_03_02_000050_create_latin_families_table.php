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
    Schema::create('latin_families', function (Blueprint $table) {
      // identifiers
      $table->id();
      $table->string('uuid', 36)->unique();
      $table->string('number', 25)->unique();

      // data
      $table->string('name', 512);

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('latin_families');
  }
};
