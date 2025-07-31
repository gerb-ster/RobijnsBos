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
    Schema::table('species_types', function (Blueprint $table) {
      $table->integer('order')->after('name');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('species_types', function (Blueprint $table) {
      $table->dropColumn('order');
    });
  }
};
