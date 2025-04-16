<?php

namespace App\Console\Commands;

use App\Models\Vegetation;
use App\Tools\BoardGenerator;
use Illuminate\Console\Command;

class TestBoard extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'test:board';

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
    $vegetation = Vegetation::where('id', 1)->first();

    $boardGenerator = new BoardGenerator($vegetation);
    $boardGenerator->render();

    return 1;
  }
}
