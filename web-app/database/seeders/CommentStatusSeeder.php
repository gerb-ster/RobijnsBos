<?php

namespace Database\Seeders;

use App\Models\CommentStatus;
use Illuminate\Database\Seeder;

class CommentStatusSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(): void
  {
    $jsonString = file_get_contents(base_path('database/data/content/comment_status.json'));
    $data = json_decode($jsonString, true); // decode the JSON into an array

    foreach ($data as $entry) {
      $object = new CommentStatus($entry);
      $object->save();
    }
  }
}
