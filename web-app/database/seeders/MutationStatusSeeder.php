<?php

namespace Database\Seeders;

use App\Models\MutationStatus;
use Illuminate\Database\Seeder;

class MutationStatusSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(): void
  {
    $jsonString = file_get_contents(base_path('database/data/content/mutation_status.json'));
    $data = json_decode($jsonString, true); // decode the JSON into an array

    foreach ($data as $entry) {
      $object = new MutationStatus($entry);
      $object->save();
    }
  }
}
