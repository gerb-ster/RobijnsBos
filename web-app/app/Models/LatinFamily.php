<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Class LatinFamily
 *
 * @property int $id
 * @property string $uuid
 * @property string $number
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Collection|Species[] $species
 *
 * @package App\Models
 */
class LatinFamily extends Model
{
	use SoftDeletes;

  /**
   * @var string
   */
	protected $table = 'latin_families';

  /**
   * @var string[]
   */
	protected $fillable = [
		'uuid',
		'number',
		'name'
	];

  /**
   * The "booting" method of the model.
   *
   * @return void
   */
  protected static function boot(): void
  {
    parent::boot();

    static::creating(function (LatinFamily $model) {
      $model->uuid = Str::uuid();

      // generate a number
      $currentMax = LatinFamily
        ::withTrashed()
        ->withoutGlobalScopes()
        ->max('id') ?? 0;

      $currentMax++;
      $counter = str_pad($currentMax, 5, "0", STR_PAD_LEFT);

      $model->number = "LF.{$counter}";
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
   * @return HasMany
   */
	public function species(): HasMany
  {
		return $this->hasMany(Species::class);
	}
}
