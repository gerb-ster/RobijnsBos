<?php

namespace App\Console\Commands;

use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DataReset extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'db:reset';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Main command to wipe, migrate, seed & import the database';

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
    config(['mail.default' => 'array']);

    $this->comment("Setup database structure and seed with structural data\n");

    if ($this->confirm('Do you wish to reset the database?', true)) {
      Artisan::call('db:wipe');
      $this->info('✔ Database wiped clean');

      Artisan::call('migrate');
      $this->info('✔ Run migrations');

      $this->info("✔ Seed the database.\n");
      Artisan::call('db:seed');
    }

    if ($this->confirm('Do you wish create some demo data?', true)) {
      // test users
      $this->call('db:seed', [
        '--class' => RoleSeeder::class,
        '--force' => true
      ]);

      $this->call('db:seed', [
        '--class' => UserSeeder::class,
        '--force' => true
      ]);
      $this->info("✔ User Demo Data generated");
    }

    $this->comment('🏁 Done!');

    return 0;
  }
}
