<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->bind('path.public', function () {
      return base_path('../dt.robijnsbos.nl/');
    });
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    //
  }
}
