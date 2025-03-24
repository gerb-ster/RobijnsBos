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
    Schema::create('vegetations', function (Blueprint $table) {
      // identifiers
      $table->id();
      $table->string('uuid', 36)->unique();
      $table->string('number', 25)->unique();
      $table->string('qr_shortcode', 6)->unique();

      // status
      $table->foreignId('status_id')->constrained('vegetation_status');

      // fields
      $table->foreignId('group_id')->constrained('groups');
      $table->foreignId('specie_id')->constrained('species');
      $table->integer('amount');

      $table->string('placed', 32)->nullable();
      $table->string('removed', 32)->nullable();

      $table->text('remarks')->nullable();
      $table->json('location')->nullable();

      // meta_data
      $table->foreignId('created_by')->constrained('users');

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('vegetations');
  }
};
