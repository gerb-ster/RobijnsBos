<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The model to policy mappings for the application.
   *
   * @var array<class-string, class-string>
   */
  protected $policies = [
    // 'App\Models\Model' => 'App\Policies\ModelPolicy',
  ];

  /**
   * Register any authentication / authorization services.
   *
   * @return void
   */
  public function boot(): void
  {
    $this->registerPolicies();

    // A Simple Gate which controls if a given user has
    // access to administration function
    Gate::define('accessBackOffice', function (User $user) {
      return in_array($user->role_id, [Role::ADMINISTRATOR, Role::OWNER]);
    });

    // A Simple Gate which controls if a given user has
    // access to administration function
    Gate::define('administrate', function (User $user) {
      return $user->role_id === Role::ADMINISTRATOR;
    });
  }
}
