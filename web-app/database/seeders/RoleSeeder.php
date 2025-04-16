<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(): void
  {
    $jsonString = file_get_contents(base_path('database/data/content/roles.json'));
    $data = json_decode($jsonString, true); // decode the JSON into an array

    foreach ($data as $entry) {
      $object = new Role($entry);
      $object->save();
    }
  }
}
