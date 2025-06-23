<?php

namespace App\Listeners;

use App\Events\VegetationDataChanged;
use App\Tools\MapGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateMap
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VegetationDataChanged $event): void
    {
      $mapGenerator = new MapGenerator();
      $mapGenerator->render();
    }
}
