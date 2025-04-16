<?php

namespace App\Models;

use App\Observers\SpeciesObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Class Species
 *
 * @property int $id
 * @property string $uuid
 * @property string $number
 * @property string $dutch_name
 * @property string $latin_name
 * @property int $type_id
 * @property int $latin_family_id
 * @property string|null $blossom_month
 * @property string|null $height
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property LatinFamily $latinFamily
 * @property SpeciesType $type
 * @property Collection|Vegetation[] $vegetation
 *
 * @package App\Models
 */
#[ObservedBy([SpeciesObserver::class])]
class Species extends Model
{
	use SoftDeletes;

  /**
   * @var string
   */
	protected $table = 'species';

  /**
   * @var string[]
   */
	protected $casts = [
		'latin_family_id' => 'int',
    'blossom_month' => 'array'
	];

  /**
   * @var string[]
   */
	protected $fillable = [
		'uuid',
		'number',
		'dutch_name',
		'latin_name',
    'type_id',
		'latin_family_id',
		'blossom_month',
		'height'
	];

  /**
   * The "booting" method of the model.
   *
   * @return void
   */
  protected static function boot(): void
  {
    parent::boot();

    static::creating(function (Species $model) {
      $model->uuid = Str::uuid();

      // generate a number
      $currentMax = Species
        ::withTrashed()
        ->withoutGlobalScopes()
        ->max('id') ?? 0;

      $currentMax++;
      $counter = str_pad($currentMax, 5, "0", STR_PAD_LEFT);

      $model->number = "S.{$counter}";
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
	public function latinFamily(): BelongsTo
  {
		return $this->belongsTo(LatinFamily::class);
	}

  /**
   * @return BelongsTo
   */
  public function type(): BelongsTo
  {
    return $this->belongsTo(SpeciesType::class, 'type_id');
  }

  /**
   * @return HasMany
   */
	public function vegetation(): HasMany
  {
		return $this->hasMany(Vegetation::class, 'specie_id');
	}
}
