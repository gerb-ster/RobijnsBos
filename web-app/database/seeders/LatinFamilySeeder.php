<?php

namespace Database\Seeders;

use App\Models\LatinFamily;
use Illuminate\Database\Seeder;

class LatinFamilySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(): void
  {
    $jsonString = file_get_contents(base_path('database/data/content/latin_families.json'));
    $data = json_decode($jsonString, true); // decode the JSON into an array

    foreach ($data as $entry) {
      $object = new LatinFamily($entry);
      $object->save();
    }
  }
}
