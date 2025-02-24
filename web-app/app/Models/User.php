<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $uuid
 * @property bool $admin
 * @property string $locale
 * @property string|null $email
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class User extends Authenticatable
{
  use HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  /**
   * The "booting" method of the model.
   *
   * @return void
   */
  protected static function boot(): void
  {
    parent::boot();

    static::creating(function ($model) {
      $model->uuid = Str::uuid();
    });
  }

  /**
   * Retrieve the model for a bound value.
   *
   * @param  mixed  $value
   * @param  string|null  $field
   * @return Model|null
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function resolveRouteBinding($value, $field = null): ?Model
  {
    return $this->where('uuid', $value)->firstOrFail();
  }
}
