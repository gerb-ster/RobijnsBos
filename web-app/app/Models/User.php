<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

/**
 * Class User
 *
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property int $role_id
 * @property string $locale
 * @property string $email
 * @property string $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Role $role
 * @property Collection|Comment[] $comments
 * @property Collection|Mutation[] $mutations
 * @property Collection|Vegetation[] $vegetation
 *
 * @package App\Models
 */
class User extends Authenticatable
{
	use SoftDeletes;

  /**
   * @var string
   */
	protected $table = 'users';

  /**
   * @var string[]
   */
	protected $casts = [
		'role_id' => 'int'
	];

  /**
   * @var string[]
   */
	protected $hidden = [
		'password'
	];

  /**
   * @var string[]
   */
	protected $fillable = [
		'uuid',
		'name',
		'role_id',
		'locale',
		'email',
		'password'
	];

  /**
   * The "booting" method of the model.
   *
   * @return void
   */
  protected static function boot(): void
  {
    parent::boot();

    static::creating(function (User $model) {
      $model->uuid = Str::uuid();
    });
  }

  /**
   * Retrieve the model for a bound value.
   *
   * @param  mixed  $value
   * @param  string|null  $field
   * @return Model|null
   */
  public function resolveRouteBinding($value, $field = null): ?Model
  {
    return $this->where('uuid', $value)->firstOrFail();
  }

  /**
   * @return BelongsTo
   */
	public function role(): BelongsTo
  {
		return $this->belongsTo(Role::class);
	}

  /**
   * @return HasMany
   */
	public function comments(): HasMany
  {
		return $this->hasMany(Comment::class, 'created_by');
	}

  /**
   * @return HasMany
   */
	public function mutations(): HasMany
  {
		return $this->hasMany(Mutation::class, 'created_by');
	}

  /**
   * @return HasMany
   */
	public function vegetation(): HasMany
  {
		return $this->hasMany(Vegetation::class, 'created_by');
	}
}
