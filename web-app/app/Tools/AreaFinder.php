<?php

namespace App\Tools;

use App\Models\Area;
use App\Models\Vegetation;

class AreaFinder
{
  /**
   * @param Vegetation $vegetation
   */
  public static function findArea(Vegetation $vegetation): void
  {
    $allAreas = Area::all();

    $allAreas->each(function (Area $area) use ($vegetation) {
      if(floatval($vegetation->location['x']) >= floatval($area->coordinates['xTop']) &&
        floatval($vegetation->location['x']) < floatval($area->coordinates['xBottom']) &&
        floatval($vegetation->location['y']) <= floatval($area->coordinates['yTop']) &&
        floatval($vegetation->location['y']) > floatval($area->coordinates['yBottom'])) {
        $vegetation->updateQuietly(['area_id' => $area->id]);
        return false;
      }
    });
  }

  /**
   * @param Area $area
   */
  public static function findVegetation(Area $area): void
  {
    $allVegetation = Vegetation::all();

    $allVegetation->each(function (Vegetation $vegetation) use ($area) {
      if(floatval($vegetation->location['x']) >= floatval($area->coordinates['xTop']) &&
         floatval($vegetation->location['x']) < floatval($area->coordinates['xBottom']) &&
         floatval($vegetation->location['y']) <= floatval($area->coordinates['yTop']) &&
         floatval($vegetation->location['y']) > floatval($area->coordinates['yBottom'])) {
        $vegetation->updateQuietly(['area_id' => $area->id]);
      }
    });
  }
}
