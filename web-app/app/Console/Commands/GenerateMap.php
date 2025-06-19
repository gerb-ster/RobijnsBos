<?php

namespace App\Console\Commands;

use App\Models\Vegetation;
use App\Tools\BoardGenerator;
use App\Tools\MapGenerator;
use Illuminate\Console\Command;

class GenerateMap extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'generate:map';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Map Generator';

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
    $mapGenerator = new MapGenerator();
    $mapGenerator->render();


    return 1;
  }
}
