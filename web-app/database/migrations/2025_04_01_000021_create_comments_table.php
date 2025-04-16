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
    Schema::create('comments', function (Blueprint $table) {
      // identifiers
      $table->id();
      $table->string('uuid', 36)->unique();
      $table->string('number', 25)->unique();

      // status
      $table->foreignId('status_id')->constrained('comment_status');

      // fields
      $table->foreignId('vegetation_id')->constrained('vegetations');

      $table->string('title', 128);
      $table->text('remarks')->default('');

      // meta_data
      $table->foreignId('created_by')->nullable()->constrained('users');

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('comments');
  }
};
