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
    Schema::table('vegetations', function (Blueprint $table) {
      $table->dropColumn('label');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('vegetations', function (Blueprint $table) {
      $table->string('label', 256)->nullable();
    });
  }
};
