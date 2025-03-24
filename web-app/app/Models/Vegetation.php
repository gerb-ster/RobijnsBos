<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class Vegetation
 *
 * @property int $id
 * @property string $uuid
 * @property string $number
 * @property int $status_id
 * @property int $group_id
 * @property int $specie_id
 * @property int $amount
 * @property Carbon|null $placed
 * @property Carbon|null $removed
 * @property string $remarks
 * @property string|null $qr_filename
 * @property string|null $location
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property User $user
 * @property Group $group
 * @property Species $species
 * @property VegetationStatus $status
 * @property Collection|Comment[] $comments
 * @property Collection|Mutation[] $mutations
 *
 * @package App\Models
 */
class Vegetation extends Model
{
	use SoftDeletes;

  /**
   * @var string
   */
	protected $table = 'vegetations';

  /**
   * @var string[]
   */
	protected $casts = [
    'location' => 'array',
		'status_id' => 'int',
		'group_id' => 'int',
		'specie_id' => 'int',
		'amount' => 'int',
		'created_by' => 'int'
	];

  /**
   * @var string[]
   */
	protected $fillable = [
		'uuid',
		'number',
    'location',
		'status_id',
		'group_id',
		'specie_id',
		'amount',
		'placed',
    'removed',
		'remarks',
		'qr_filename',
		'created_by'
	];

  /**
   * The "booting" method of the model.
   *
   * @return void
   */
  protected static function boot(): void
  {
    parent::boot();

    static::creating(function (Vegetation $model) {
      $model->uuid = Str::uuid();
      $model->status_id = VegetationStatus::TO_BO_PLANTED;

      // generate a number
      $currentMax = Vegetation
        ::withTrashed()
        ->withoutGlobalScopes()
        ->max('id') ?? 0;

      $currentMax++;
      $counter = str_pad($currentMax, 5, "0", STR_PAD_LEFT);

      if(is_null($model->created_by)) {
        $model->created_by = Auth::user()->id;
      }

      $model->number = "P.{$counter}";
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
	public function user(): BelongsTo
  {
		return $this->belongsTo(User::class, 'created_by');
	}

  /**
   * @return BelongsTo
   */
	public function group(): BelongsTo
  {
		return $this->belongsTo(Group::class);
	}

  /**
   * @return BelongsTo
   */
	public function species(): BelongsTo
  {
		return $this->belongsTo(Species::class, 'specie_id');
	}

  /**
   * @return BelongsTo
   */
	public function status(): BelongsTo
  {
		return $this->belongsTo(VegetationStatus::class, 'status_id');
	}

  /**
   * @return HasMany
   */
	public function comments(): HasMany
  {
		return $this->hasMany(Comment::class);
	}

  /**
   * @return HasMany
   */
	public function mutations(): HasMany
  {
		return $this->hasMany(Mutation::class);
	}
}
