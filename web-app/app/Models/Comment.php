<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Class Comment
 *
 * @property int $id
 * @property string $uuid
 * @property string $number
 * @property int $status_id
 * @property int $vegetation_id
 * @property string $name
 * @property string $remarks
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property User|null $user
 * @property CommentStatus $status
 * @property Vegetation $vegetation
 *
 * @package App\Models
 */
class Comment extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * @var string
   */
	protected $table = 'comments';

  /**
   * @var string[]
   */
	protected $casts = [
		'status_id' => 'int',
		'vegetation_id' => 'int',
		'created_by' => 'int'
	];

  /**
   * @var string[]
   */
	protected $fillable = [
		'uuid',
		'number',
		'status_id',
		'vegetation_id',
		'name',
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

    static::creating(function (Comment $model) {
      $model->uuid = Str::uuid();
      $model->status_id = CommentStatus::APPROVED;

      // generate a number
      $currentMax = Comment
        ::withTrashed()
        ->withoutGlobalScopes()
        ->max('id') ?? 0;

      $currentMax++;
      $counter = str_pad($currentMax, 5, "0", STR_PAD_LEFT);

      $model->number = "C.{$counter}";
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
	public function status(): BelongsTo
  {
		return $this->belongsTo(CommentStatus::class, 'status_id');
	}

  /**
   * @return BelongsTo
   */
	public function vegetation(): BelongsTo
  {
		return $this->belongsTo(Vegetation::class);
	}
}
