<?php

namespace App\Console\Commands;

use App\Models\Vegetation;
use App\Tools\BoardGenerator;
use Illuminate\Console\Command;

class FixCoords extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'fix:coords';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Test Board Generator';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle(): int
  {
    $allVegetation = Vegetation::all();

    foreach ($allVegetation as $vegetation) {
      $coords = json_encode($vegetation->location);

      $fixedCoords = $vegetation->location;
      $fixedCoords['x'] = round($fixedCoords['x'], 1);
      $fixedCoords['y'] = round($fixedCoords['y'], 1);

      $fixedCoordsStr = json_encode($fixedCoords);

      $vegetation->update(['location' => $fixedCoords]);

      $this->info("{$vegetation->id} -> {$coords} -> {$fixedCoordsStr}");
    }

    return 1;
  }
}
