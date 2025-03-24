<?php

namespace App\Observers;

use App\Models\LatinFamily;
use App\Models\Species;
use Illuminate\Support\Str;

class SpeciesObserver
{
  /**
   * Handle the Species "created" event.
   */
  public function created(Species $species): void
  {
    if ($species->latin_name) {
      $latinFamilyName = Str::lower(Str::trim(Str::words($species->latin_name, 1, "")));

      $latinFamily = LatinFamily::firstOrCreate([
        'name' => $latinFamilyName
      ]);

      $species->latin_family_id = $latinFamily->id;
      $species->saveQuietly();
    }
  }

  /**
   * Handle the Species "updated" event.
   */
  public function updated(Species $species): void
  {
    if ($species->isDirty('latin_name') && $species->latin_name) {
      $latinFamilyName = Str::lower(Str::trim(Str::words($species->latin_name, 1)));

      $latinFamily = LatinFamily::firstOrCreate([
        'name' => $latinFamilyName
      ]);

      $species->latin_family_id = $latinFamily->id;
      $species->saveQuietly();
    }
  }

  /**
   * Handle the Species "deleted" event.
   */
  public function deleted(Species $species): void
  {
    //
  }

  /**
   * Handle the Species "restored" event.
   */
  public function restored(Species $species): void
  {
    //
  }

  /**
   * Handle the Species "force deleted" event.
   */
  public function forceDeleted(Species $species): void
  {
    //
  }
}
