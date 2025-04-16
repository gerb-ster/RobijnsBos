<?php

namespace App\Console\Commands;

use Database\Seeders\AreaSeeder;
use Database\Seeders\CommentSeeder;
use Database\Seeders\GroupSeeder;
use Database\Seeders\MutationSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\VegetationStatusSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateDemoData extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'create:demoData';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create demo data';

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
    $this->comment("Create some demo data\n\n");

    $this->call('db:seed', ['--class' => CommentSeeder::class, '--force' => true]);
    $this->call('db:seed', ['--class' => MutationSeeder::class, '--force' => true]);

    $this->comment('🏁 Done!');

    return 0;
  }
}
