<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class VegetationStatus
 *
 * @property int $id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|Vegetation[] $vegetations
 *
 * @package App\Models
 */
class VegetationStatus extends Model
{
  const TO_BO_PLANTED = 1;
  const PLANTED = 2;
  const REMOVED = 3;

	protected $table = 'vegetation_status';

  /**
   * @var string[]
   */
	protected $fillable = [
		'name'
	];

  /**
   * @return HasMany
   */
	public function vegetations(): HasMany
  {
		return $this->hasMany(Vegetation::class, 'status_id');
	}
}
