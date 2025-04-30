<?php

namespace App\Models;

use App\Tools\BoardGenerator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Class Vegetation
 *
 * @property int $id
 * @property string $uuid
 * @property string $number
 * @property string $qr_shortcode
 * @property int $status_id
 * @property int $group_id
 * @property int $specie_id
 * @property string $label
 * @property int $amount
 * @property Carbon|null $placed
 * @property Carbon|null $removed
 * @property string $remarks
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
    'qr_shortcode',
    'location',
		'status_id',
    'label',
		'group_id',
		'specie_id',
		'amount',
		'placed',
    'removed',
		'remarks',
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
      $model->qr_shortcode = Str::random(6);

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

      if(is_null($model->status_id)) {
        $model->status_id = VegetationStatus::TO_BO_PLANTED;
      }

      $model->number = "P.{$counter}";
    });

    static::created(function (Vegetation $model) {
      $model->generateQRCodeFile();

      $boardGenerator = new BoardGenerator($model);
      $boardGenerator->render();
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
   * Retrieve the model for a bound value.
   *
   * @param $childType
   * @param mixed $value
   * @param null $field
   * @return Model|null
   */
  public function resolveChildRouteBinding($childType, $value, $field = null): ?Model
  {
    if ($childType === 'comment') {
      return $this->comments()->where('uuid', $value)->firstOrFail();
    }

    if ($childType === 'mutation') {
      return $this->mutations()->where('uuid', $value)->firstOrFail();
    }

    return parent::resolveChildRouteBinding($childType, $value, $field);
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

  /**
   * @return void
   */
  public function generateQRCodeFile(): void
  {
    $data = QrCode::size(512)
      ->format('svg')
      ->merge('/public/images/logo.png')
      ->errorCorrection('M')
      ->generate(
        route('public.vegetation.redirect', ['shortCode' => $this->qr_shortcode])
      );

    if (!is_dir(public_path(env('QR_CODES_PATH')))) {
      mkdir(public_path(env('QR_CODES_PATH')));
    }

    file_put_contents(public_path(env('QR_CODES_PATH').$this->uuid.'.svg'), $data);
  }
}
