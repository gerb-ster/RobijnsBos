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
    Schema::table('comments', function (Blueprint $table) {
      $table->renameColumn('title', 'name');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('comments', function (Blueprint $table) {
      $table->renameColumn('name', 'title');
    });
  }
};
