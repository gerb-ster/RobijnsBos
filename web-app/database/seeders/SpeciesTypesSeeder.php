<?php

namespace Database\Seeders;

use App\Models\CommentStatus;
use App\Models\SpeciesType;
use Illuminate\Database\Seeder;

class SpeciesTypesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(): void
  {
    $jsonString = file_get_contents(base_path('database/data/content/species_types.json'));
    $data = json_decode($jsonString, true); // decode the JSON into an array

    foreach ($data as $entry) {
      $object = new SpeciesType($entry);
      $object->save();
    }
  }
}
