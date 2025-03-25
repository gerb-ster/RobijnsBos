<?php

namespace Database\Seeders;

use App\Models\Mutation;
use App\Models\User;
use App\Models\Vegetation;
use Illuminate\Database\Seeder;

class MutationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(): void
  {
    foreach (Vegetation::all() as $vegetation) {
      Mutation::factory()->create([
        'vegetation_id' => $vegetation->id,
        'created_by' => User::all()->random()->id,
      ]);
    }
  }
}
