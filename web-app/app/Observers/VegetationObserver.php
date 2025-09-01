<?php

namespace App\Observers;

use App\Models\Vegetation;
use App\Tools\AreaFinder;

class VegetationObserver
{
  /**
   * Handle the Vegetation "created" event.
   */
  public function created(Vegetation $vegetation): void
  {
    AreaFinder::findArea($vegetation);
  }

  /**
   * Handle the Vegetation "updated" event.
   */
  public function updated(Vegetation $vegetation): void
  {
    AreaFinder::findArea($vegetation);
  }

  /**
   * Handle the Vegetation "deleted" event.
   */
  public function deleted(Vegetation $vegetation): void
  {
    //
  }

  /**
   * Handle the Vegetation "restored" event.
   */
  public function restored(Vegetation $vegetation): void
  {
    //
  }

  /**
   * Handle the Vegetation "force deleted" event.
   */
  public function forceDeleted(Vegetation $vegetation): void
  {
    //
  }
}
