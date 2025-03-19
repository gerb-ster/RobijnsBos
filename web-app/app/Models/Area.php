<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Area
 *
 * @property int $id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|Group[] $groups
 *
 * @package App\Models
 */
class Area extends Model
{
  use SoftDeletes;

  /**
   * @var string
   */
	protected $table = 'areas';

  /**
   * @var string[]
   */
	protected $fillable = [
		'name'
	];

  /**
   * @return HasMany
   */
	public function groups(): HasMany
  {
		return $this->hasMany(Group::class);
	}
}
