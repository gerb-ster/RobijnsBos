<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\User;
use App\Models\Vegetation;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(): void
  {
    foreach (Vegetation::all() as $vegetation) {
      Comment
        ::factory()
        ->count(5)
        ->create([
          'vegetation_id' => $vegetation->id,
          'created_by' => User::all()->random()->id,
        ]);
    }
  }
}
