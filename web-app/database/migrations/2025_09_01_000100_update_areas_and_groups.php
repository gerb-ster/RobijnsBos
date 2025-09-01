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
    Schema::table('areas', function (Blueprint $table) {
      $table->json('coordinates')->after('name');
    });

    Schema::table('vegetations', function (Blueprint $table) {
      $table->dropForeign(['group_id']);
      $table->dropColumn('group_id');
      $table->foreignId('area_id')->nullable()->after('status_id')->constrained('areas');
    });

    Schema::dropIfExists('groups');
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
  }
};
