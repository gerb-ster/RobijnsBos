<?php

namespace App\Console\Commands;

use Database\Seeders\AreaSeeder;
use Database\Seeders\GroupSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\VegetationStatusSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DataDump extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'db:dump';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Dump data to JSON files';

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
    // make sure we're not going to send any emails
    $this->comment("Dump content files to JSON\n");

    //$this->call('model:export', ['model' => 'User', '--path' => 'database/data/content', '--filename' => 'users', '--with-hidden' => true]);
    $this->call('model:export', ['model' => 'Area', '--path' => 'database/data/content', '--filename' => 'areas']);
    $this->call('model:export', ['model' => 'Group', '--path' => 'database/data/content', '--filename' => 'groups']);
    $this->call('model:export', ['model' => 'Role', '--path' => 'database/data/content', '--filename' => 'roles']);
    $this->call('model:export', ['model' => 'LatinFamily', '--path' => 'database/data/content', '--filename' => 'latin_families']);
    $this->call('model:export', ['model' => 'Species', '--path' => 'database/data/content', '--filename' => 'species']);
    $this->call('model:export', ['model' => 'VegetationStatus', '--path' => 'database/data/content', '--filename' => 'vegetation_status']);

    $this->comment('🏁 Done!');

    return 0;
  }
}
