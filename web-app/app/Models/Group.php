<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Group
 *
 * @property int $id
 * @property string|null $name
 * @property int $area_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Area $area
 * @property Collection|Vegetation[] $vegetations
 *
 * @package App\Models
 */
class Group extends Model
{
  /**
   * @var string
   */
	protected $table = 'groups';

  /**
   * @var string[]
   */
	protected $casts = [
		'area_id' => 'int'
	];

  /**
   * @var string[]
   */
	protected $fillable = [
		'name',
		'area_id'
	];

  /**
   * @return BelongsTo
   */
	public function area(): BelongsTo
  {
		return $this->belongsTo(Area::class);
	}

  /**
   * @return HasMany
   */
	public function vegetations(): HasMany
  {
		return $this->hasMany(Vegetation::class);
	}
}
